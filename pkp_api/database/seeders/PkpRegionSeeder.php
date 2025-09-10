<?php

namespace Database\Seeders;

use App\Models\Pkp_region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PkpRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'region_name' => 'Region I (Ilocos Region)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region II (Cagayan Valley)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region III (Central Luzon)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region IV-A (CALABARZON)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region V (Bicol Region)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region VI (Western Visayas)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region VII (Central Visayas)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region VIII (Eastern Visayas)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region IX (Zamboanga Peninzula)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region X (Northern Mindanao)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region XI (Davao Region)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region XII (SOCCSKSARGEN)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'National Capital Region (NCR)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Cordillera Administrative Region (CAR)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Bangsamoro Autonomous Region in Muslim Mindanao ',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region XIII (Caraga)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'region_name' => 'Region IV-B (MIMAROPA)',
                'uid' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        Pkp_region::insert($data);
    }
}
