<?php

namespace App\Http\Controllers;

use App\Helpers\MailLogger;
use App\Mail\OtpMail;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    // Step 1: Show email form
    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    // Step 2: Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Delete any old OTPs for this email
        PasswordOtp::where('email', $request->email)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Save to DB — expires in 5 minutes
        PasswordOtp::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Send OTP email and log it
        $logResult = MailLogger::log(
            'otp',
            $request->email,
            $user->name,
            $user->id,
            fn() => Mail::to($request->email)->send(new OtpMail($otp, $user->name)),
            'forgot_password',
            ['otp_code' => $otp, 'otp_verified' => false]
        );

        if (!$logResult->success) {
            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }

        // Store email in session for next step
        session(['otp_email' => $request->email]);

        return redirect()->route('password.otp.form')
            ->with('success', 'OTP sent to ' . $request->email);
    }

    // Step 3: Show OTP form
    public function showOtpForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('password.forgot');
        }

        $otpRecord = PasswordOtp::where('email', session('otp_email'))
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $secondsLeft = $otpRecord ? $otpRecord->secondsRemaining() : 0;

        return view('auth.otp-verify', [
            'email'      => session('otp_email'),
            'secondsLeft' => $secondsLeft,
        ]);
    }

    // Step 4: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.forgot');
        }

        $otpRecord = PasswordOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // Mark OTP as used
        $otpRecord->update(['used' => true]);

        // Mark mail log OTP as verified
        MailLogger::markOtpVerified($email);

        // Store verified flag in session
        session(['otp_verified' => true, 'otp_email' => $email]);

        return redirect()->route('password.reset.form');
    }

    // Step 5: Show new password form
    public function showResetForm()
    {
        if (!session('otp_verified') || !session('otp_email')) {
            return redirect()->route('password.forgot');
        }
        return view('auth.reset-password', ['email' => session('otp_email')]);
    }

    // Step 6: Reset password
    public function resetPassword(Request $request)
    {
        if (!session('otp_verified') || !session('otp_email')) {
            return redirect()->route('password.forgot');
        }

        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', session('otp_email'))->first();
        if (!$user) {
            return redirect()->route('password.forgot');
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Clear session
        session()->forget(['otp_email', 'otp_verified']);

        return redirect()->route('login')
            ->with('success', '✅ Password reset successfully! Please log in with your new password.');
    }

    // Resend OTP
    public function resendOtp()
    {
        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('password.forgot');
        }

        // Delete old OTPs
        PasswordOtp::where('email', $email)->delete();

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        PasswordOtp::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        $logResult = MailLogger::log(
            'otp_resend',
            $email,
            $user->name,
            $user->id,
            fn() => Mail::to($email)->send(new OtpMail($otp, $user->name)),
            'forgot_password',
            ['otp_code' => $otp, 'otp_verified' => false]
        );

        if (!$logResult->success) {
            return back()->withErrors(['otp' => 'Failed to resend OTP.']);
        }

        return back()->with('success', 'New OTP sent to ' . $email);
    }
}
