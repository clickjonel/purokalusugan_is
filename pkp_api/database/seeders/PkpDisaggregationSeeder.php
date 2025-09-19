<?php

namespace Database\Seeders;

use App\Models\Pkp_disaggregation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PkpDisaggregationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Male, Female, Not Identified, 4Ps and Household
        $data = [
            [
                'disaggregation_code' => 'DIS-25-01',
                'disaggregation_name' => 'Total',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'disaggregation_code' => 'DIS-25-02',
                'disaggregation_name' => 'Male',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'disaggregation_code' => 'DIS-25-03',
                'disaggregation_name' => 'Female',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'disaggregation_code' => 'DIS-25-04',
                'disaggregation_name' => 'Not Identified',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'disaggregation_code' => 'DIS-25-05',
                'disaggregation_name' => '4Ps',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'disaggregation_code' => 'DIS-25-05',
                'disaggregation_name' => 'Household',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        
        Pkp_disaggregation::insert($data);
    }
}
