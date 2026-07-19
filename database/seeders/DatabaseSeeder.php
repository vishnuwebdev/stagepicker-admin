<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// This repo never had a database/seeders directory before — adding the
// standard Laravel entry point so `php artisan db:seed` (with no --class
// flag) works too, not just `db:seed --class=MerchandiseSeeder`.
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            MerchandiseSeeder::class,
        ]);
    }
}
