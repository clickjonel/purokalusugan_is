<?php

namespace Database\Seeders;

use App\Models\Programs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'program_name' => 'Nutrition Program',
                'program_code' => 'NP',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Immunization Program',
                'program_code' => 'IP',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Water, Sanitation and Hygiene (WASH) Program',
                'program_code' => 'WASH',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Tuberculosis (TB) Program',
                'program_code' => 'TB',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'HIV and AIDS  Program',
                'program_code' => 'HIV',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Road Safety Program',
                'program_code' => 'RSP',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Maternal Health Program',
                'program_code' => 'MHP',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Non-Communicable Diseases Program',
                'program_code' => 'NCD',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_name' => 'Mental Health',
                'program_code' => 'MH',
                'program_status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Programs::insert($programs);

    }
}
