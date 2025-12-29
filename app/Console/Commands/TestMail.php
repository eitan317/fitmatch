<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class TestMail extends Command
{
    protected $signature = 'mail:test {email}';
    protected $description = 'Test sending email to verify mail configuration';

    public function handle()
    {
        $email = $this->argument('email');
        $code = '123456';

        $this->info('Testing mail configuration...');
        $this->info('Mailer: ' . config('mail.default'));
        $this->info('Host: ' . config('mail.mailers.smtp.host'));
        $this->info('Port: ' . config('mail.mailers.smtp.port'));
        $this->info('From: ' . config('mail.from.address'));
        $this->info('Sending to: ' . $email);

        try {
            Mail::to($email)->send(new EmailVerificationMail($code));
            $this->info('Email sent successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error sending email: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

