<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = collect([
            [
                'team_name' => 'Team A',
                // 'team_scope' => [1, 2, 3],
                // 'team_members' => [1, 2, 3]
            ],
            [
                'team_name' => 'Team B',
                // 'team_scope' => [4, 5, 6],
                // 'team_members' => [4, 5, 6]
            ],
            [
                'team_name' => 'Team C',
                // 'team_scope' => [7,8,9],
                // 'team_members' => [7,8,9]
            ],
        ]);

        $teams->each(function($team){
            Team::create($team);
        });
    }
}
