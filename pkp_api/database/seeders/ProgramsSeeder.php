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
                'name' => 'Nutrition Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Immunization Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Water, Sanitation and Hygiene (WASH) Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tuberculosis (TB) Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HIV and AIDS  Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Road Safety Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maternal Health Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Non-Communicable Diseases Program',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mental Health',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Programs::insert($programs);

    }
}
