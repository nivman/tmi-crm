<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Mail::fake();
        // Telescope::stopRecording();
        $this->call(UsersTableSeeder::class);
        $this->call(MiscDataSeeder::class);
        $this->call(InvoicesTableSeeder::class);
        $this->call(PurchasesTableSeeder::class);
        $this->call(RecurringsTableSeeder::class);
    }
}
