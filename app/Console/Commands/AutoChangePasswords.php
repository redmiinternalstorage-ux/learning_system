<?php

namespace App\Console\Commands;

use App\Helpers\MailLogger;
use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AutoChangePasswords extends Command
{
    protected $signature   = 'passwords:auto-change';
    protected $description = 'Auto-change passwords for users whose scheduled date is today';

    public function handle(): int
    {
        $today = now()->toDateString(); // YYYY-MM-DD

        $users = User::where('auto_change_pass', true)
            ->whereDate('auto_change_pass_date', $today)
            ->whereNotNull('auto_change_pass_value')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No scheduled password changes for today.');
            return self::SUCCESS;
        }

        $changed = 0;

        foreach ($users as $user) {
            // The stored value is already hashed — set it as the new password
            // But we need the plain value at save time; see note in ProfileController.
            // We store the HASHED value, so we directly assign without re-hashing.
            \DB::table('users')->where('id', $user->id)->update([
                'password'              => $user->auto_change_pass_value,
                // Clear the scheduled change after it runs
                'auto_change_pass'      => false,
                'auto_change_pass_date' => null,
                'auto_change_pass_value'=> null,
            ]);

            // Send notification email and log it
            MailLogger::log(
                'auto_password_changed',
                $user->email,
                $user->name,
                $user->id,
                fn() => Mail::to($user->email)->send(new PasswordChangedMail(
                    $user->name,
                    now()->format('d M Y, h:i A')
                )),
                'auto_schedule'
            );

            $this->info("✓ Password auto-changed for: {$user->name} ({$user->email})");
            $changed++;
        }

        $this->info("Done — {$changed} password(s) changed.");
        return self::SUCCESS;
    }
}
