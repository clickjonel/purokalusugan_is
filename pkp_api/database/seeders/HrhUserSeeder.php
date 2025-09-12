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
        $hrh = DB::connection('dohis')->table('pkp_user')->get();
        $hrh_server = DB::connection('dohis-server')->table('dohis_hrh_profile')->limit(20)->get();

        $hrh->each(function ($hrh) {
            Hrh::create([
                'user_code' => $hrh->user_code,
                // 'image' => null,
                'username' => $hrh->username,
                'password' => $hrh->password,
                'first_name' => $hrh->first_name,
                'middle_name' => $hrh->middle_name ?? null,
                'last_name' => $hrh->last_name,
                'suffix' => $hrh->suffix ?? null,
                'nickname' => $hrh->nickname,
                'account_status' => $hrh->account_status,
                'user_level' => 5
            ]);
        });
        
        
        $hrh_server->each(function ($hrh) {
            Hrh::create([
                'user_code' => $hrh->user_code,
                // 'image' => null,
                'username' => $hrh->email_address,
                'password' => bcrypt('12345'),
                'first_name' => $hrh->first_name,
                'middle_name' => $hrh->middle_name ?? null,
                'last_name' => $hrh->last_name,
                'suffix' => $hrh->suffix ?? null,
                'nickname' => $hrh->nickname,
                'account_status' => 'Active',
                'user_level' => 5
            ]);
        });

    }
}
