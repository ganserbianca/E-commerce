<?php

namespace Database\Seeders;

use App\Models\Audience;
use Illuminate\Database\Seeder;

class AudienceSeeder extends Seeder
{
    public function run(): void
    {
        Audience::firstOrCreate(['name' => 'Adulto']);
        Audience::firstOrCreate(['name' => 'Infantil']);
    }
}
