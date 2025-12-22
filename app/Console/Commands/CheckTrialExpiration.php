<?php

namespace App\Console\Commands;

use App\Models\Trainer;
use Illuminate\Console\Command;

class CheckTrialExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainers:check-trial-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update trainers with expired trial period to pending_payment status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired trials...');

        $expiredTrials = Trainer::where('status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredTrials as $trainer) {
            $trainer->update(['status' => 'pending_payment']);
            $count++;
            $this->line("Updated trainer {$trainer->id} ({$trainer->full_name}) to pending_payment");
        }

        if ($count > 0) {
            $this->info("Updated {$count} trainer(s) to pending_payment status.");
        } else {
            $this->info('No expired trials found.');
        }

        return Command::SUCCESS;
    }
}

