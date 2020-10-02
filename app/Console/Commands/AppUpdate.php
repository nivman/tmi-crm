<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppUpdate extends Command
{
    protected $description = 'Update app database to the latest version';
    protected $signature   = 'app:update';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // set_time_limit(300);
        $this->line('Migration started...');
        try {
            \Artisan::call('migrate', ['--force' => true, '--path' => 'database/migrations/updates']);
            $this->line(trim(\Artisan::output()));
        } catch (\Exception $e) {
            $this->error('E001: Migration failed! ' . $e->getMessage());
        }
    }
}
