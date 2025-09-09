<?php

namespace Database\Seeders;

use App\HRHTrait;
use App\Models\Hrh;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrhUserSeeder extends Seeder
{

    use HRHTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hrh = DB::connection('dohis')->table('dohis_hrh_user')->get();
        
        // $hrh->each(function ($hrh){
        //     Hrh::create([
        //         'user_code' => "DOH".uniqid(),
        //         'image' => null,
        //         'username' => $this->formatUsername($hrh->first_name, $hrh->middle_name ?? null, $hrh->last_name),
        //         'password' => bcrypt('12345'),
        //         'first_name' => $hrh->first_name,
        //         'middle_name' => $hrh->middle_name ?? null,
        //         'last_name' => $hrh->last_name,
        //         'suffix' => $hrh->suffix ?? null,
        //         'nickname' => $hrh->nickname,
        //         'account_status' => 'unassigned',
        //         'user_level' => 5
        //     ]);
        // });

    }
}
