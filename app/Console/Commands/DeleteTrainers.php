<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;

class DeleteTrainers extends Command
{
    protected $signature = 'trainers:delete {--ids=* : Specific trainer IDs to delete} {--status= : Delete by status (active, trial, etc.)} {--all-active : Delete all active trainers} {--list : List all trainers}';
    
    protected $description = 'Delete trainers from the database';

    public function handle()
    {
        if ($this->option('list')) {
            $this->listTrainers();
            return 0;
        }

        if ($this->option('all-active')) {
            return $this->deleteByStatus(['active', 'trial']);
        }

        if ($this->option('status')) {
            return $this->deleteByStatus([$this->option('status')]);
        }

        if ($this->option('ids')) {
            return $this->deleteByIds($this->option('ids'));
        }

        $this->error('Please specify --ids, --status, or --all-active');
        $this->info('Usage examples:');
        $this->info('  php artisan trainers:delete --list');
        $this->info('  php artisan trainers:delete --ids=1,2,3');
        $this->info('  php artisan trainers:delete --status=active');
        $this->info('  php artisan trainers:delete --all-active');
        return 1;
    }

    protected function listTrainers()
    {
        $trainers = Trainer::select('id', 'full_name', 'status', 'city', 'created_at')
            ->orderBy('id')
            ->get();

        if ($trainers->isEmpty()) {
            $this->info('No trainers found.');
            return;
        }

        $this->info('All Trainers:');
        $this->table(
            ['ID', 'Name', 'Status', 'City', 'Created'],
            $trainers->map(function ($trainer) {
                return [
                    $trainer->id,
                    $trainer->full_name,
                    $trainer->status,
                    $trainer->city,
                    $trainer->created_at->format('Y-m-d'),
                ];
            })->toArray()
        );
    }

    protected function deleteByIds(array $ids)
    {
        $ids = array_map('intval', $ids);
        $trainers = Trainer::whereIn('id', $ids)->get();

        if ($trainers->isEmpty()) {
            $this->error('No trainers found with the specified IDs.');
            return 1;
        }

        $this->info('Found ' . $trainers->count() . ' trainer(s):');
        foreach ($trainers as $trainer) {
            $this->line("  - ID {$trainer->id}: {$trainer->full_name} (Status: {$trainer->status})");
        }

        if (!$this->confirm('Are you sure you want to delete these trainers?')) {
            $this->info('Cancelled.');
            return 0;
        }

        DB::transaction(function () use ($ids) {
            // Delete reviews
            DB::table('reviews')->whereIn('trainer_id', $ids)->delete();
            
            // Delete trainers
            DB::table('trainers')->whereIn('id', $ids)->delete();
        });

        $this->info('Trainers deleted successfully!');
        return 0;
    }

    protected function deleteByStatus(array $statuses)
    {
        $trainers = Trainer::whereIn('status', $statuses)->get();

        if ($trainers->isEmpty()) {
            $this->info('No trainers found with the specified status(es).');
            return 0;
        }

        $this->info('Found ' . $trainers->count() . ' trainer(s) with status(es): ' . implode(', ', $statuses));
        
        if (!$this->confirm('Are you sure you want to delete ALL these trainers?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $ids = $trainers->pluck('id')->toArray();

        DB::transaction(function () use ($ids) {
            // Delete reviews
            DB::table('reviews')->whereIn('trainer_id', $ids)->delete();
            
            // Delete trainers
            DB::table('trainers')->whereIn('id', $ids)->delete();
        });

        $this->info('Trainers deleted successfully!');
        return 0;
    }
}

