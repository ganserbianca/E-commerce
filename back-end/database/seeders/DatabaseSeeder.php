<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ClientSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            AudienceSeeder::class,
            ProductSeeder::class,
            AddressSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
