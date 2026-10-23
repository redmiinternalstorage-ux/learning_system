<?php

namespace App\Console\Commands;

use App\Mail\AccountBlockedMail;
use App\Mail\AccountDeletedMail;
use App\Mail\AccountUnblockedMail;
use App\Mail\OtpMail;
use App\Mail\PasswordChangedMail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestAllMails extends Command
{
    protected $signature   = 'mail:test {email? : Target email address}';
    protected $description = 'Send all mail types to a test email address one by one';

    public function handle(): int
    {
        $target = $this->argument('email') ?? 'digitalmarketingfoundationno1@gmail.com';

        // Use a dummy user object — no DB needed
        $userName = 'Test User';
        try {
            $dbUser = User::where('role', 'admin')->first() ?? User::first();
            if ($dbUser) $userName = $dbUser->name;
        } catch (\Exception $e) {
            // DB not available — use dummy name
        }

        $user = (object)['name' => $userName, 'email' => $target];

        $this->info("📧 Sending all mails to: {$target}");
        $this->newLine();

        $mails = [
            '1. WelcomeMail'          => fn() => Mail::to($target)->send(new WelcomeMail(
                                             (new User)->forceFill(['name' => $userName, 'email' => $target])
                                         )),
            '2. OtpMail'              => fn() => Mail::to($target)->send(new OtpMail('847291', $userName)),
            '3. PasswordChangedMail'  => fn() => Mail::to($target)->send(new PasswordChangedMail($userName, now()->format('d M Y, h:i A'))),
            '4. AccountBlockedMail'   => fn() => Mail::to($target)->send(new AccountBlockedMail($userName, $target)),
            '5. AccountUnblockedMail' => fn() => Mail::to($target)->send(new AccountUnblockedMail($userName, $target)),
            '6. AccountDeletedMail'   => fn() => Mail::to($target)->send(new AccountDeletedMail($userName, $target)),
        ];

        foreach ($mails as $label => $send) {
            $this->info("Sending {$label}...");
            try {
                $send();
                $this->line("  <fg=green>✓ Sent successfully!</>");
            } catch (\Exception $e) {
                $this->line("  <fg=red>✗ Failed: {$e->getMessage()}</>");
            }
            $this->newLine();
            sleep(1);
        }

        $this->info("✅ All done! Check inbox: {$target}");
        return self::SUCCESS;
    }
}
