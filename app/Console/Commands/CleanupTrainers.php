<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;

class CleanupTrainers extends Command
{
    protected $signature = 'trainers:cleanup {--confirm : Skip confirmation}';
    protected $description = 'Delete all active and trial trainers (cleanup unwanted trainers)';

    public function handle()
    {
        $trainers = Trainer::whereIn('status', ['active', 'trial'])->get();
        
        if ($trainers->isEmpty()) {
            $this->info('No active/trial trainers found.');
            return 0;
        }

        $this->info('Found ' . $trainers->count() . ' trainer(s) to delete:');
        foreach ($trainers as $trainer) {
            $this->line("  - ID {$trainer->id}: {$trainer->full_name} (Status: {$trainer->status})");
        }

        if (!$this->option('confirm')) {
            if (!$this->confirm('Are you sure you want to delete ALL these trainers?')) {
                $this->info('Cancelled.');
                return 0;
            }
        }

        $ids = $trainers->pluck('id')->toArray();

        DB::transaction(function () use ($ids) {
            DB::table('reviews')->whereIn('trainer_id', $ids)->delete();
            DB::table('trainers')->whereIn('id', $ids)->delete();
        });

        $this->info('✅ All trainers deleted successfully!');
        return 0;
    }
}

