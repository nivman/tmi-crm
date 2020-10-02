<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetData extends Command
{
    protected $signature = 'data:reset {--force} {--seed}';

    protected $description = 'Truncate all the tables in database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('force')) {
            if ($this->confirm('It will delete all the data from your database. Do you have Developer license?')) {
                $this->migrateFresh($this->option('seed'));
            }
        } elseif (demo()) {
            $this->migrateFresh(true);
        } else {
            $this->line('I do not have anything to do! Do I?');
        }
    }

    protected function migrateFresh($seed) {
        $this->line('Migration started...');
        set_time_limit(300);
        \Artisan::call('migrate:fresh', [
            '--force' => true,
            '--seed' => $seed,
        ]);
    }
}
