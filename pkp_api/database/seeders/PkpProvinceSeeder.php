<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PkpProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'province_id'   => 1401,
                'region_id'     => 14,
                'province_name' => 'Abra',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
            [
                'province_id'   => 1411,
                'region_id'     => 14,
                'province_name' => 'Benguet',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
            [
                'province_id'   => 1427,
                'region_id'     => 14,
                'province_name' => 'Ifugao',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
            [
                'province_id'   => 1432,
                'region_id'     => 14,
                'province_name' => 'Kalinga',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
            [
                'province_id'   => 1444,
                'region_id'     => 14,
                'province_name' => 'Mountain Province',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
            [
                'province_id'   => 1481,
                'region_id'     => 14,
                'province_name' => 'Apayao',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
            [
                'province_id'   => 1430,
                'region_id'     => 14,
                'province_name' => 'City of Baguio (Capital)',
                'uid'           => 1,
                'created_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
                'updated_at'    => Carbon::createFromFormat('d/m/Y H:i:s', '20/7/2023 11:20:56'),
            ],
        ];

        DB::connection('pkpulse')->table('pkp_province')->insert($data);
    }
}
