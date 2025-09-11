<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProgramsSeeder::class,
            // HrhUserSeeder::class,
            PkpRegionSeeder::class,
            PkpProvinceSeeder::class,
            PkpMunicipalitySeeder::class,
            PkpBarangaySeeder::class,
            TeamSeeder::class,
            PkpDisaggregationSeeder::class,
            PkpIndicatorSeeder::class
        ]);
    }
}