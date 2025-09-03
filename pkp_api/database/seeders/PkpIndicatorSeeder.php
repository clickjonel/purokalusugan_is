<?php
namespace Database\Seeders;
use App\Models\Pkp_indicator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class PkpIndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indicators = [
            // Program 1 indicators
            [
                'program_id' => 1,
                'indicator_code' => null,
                'indicator_name' => 'Breastfeeding',
                'indicator_description' => 'No. of pregnant and lactating women counseled on breastfeeding',
                'indicator_scope' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_id' => 1,
                'indicator_code' => null,
                'indicator_name' => 'Nutritional Assessment',
                'indicator_description' => 'No. of 0-59 months old children assessed',
                'indicator_scope' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_id' => 1,
                'indicator_code' => null,
                'indicator_name' => 'Vitamin A Supplementation',
                'indicator_description' => 'No. of children 6-59 months given Vitamin A',
                'indicator_scope' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'program_id' => 1,
                'indicator_code' => null,
                'indicator_name' => 'LNS-SQ Supplementation',
                'indicator_description' => 'No. of children 6-59 months old given with LNS-SQ',
                'indicator_scope' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Pkp_indicator::insert($indicators);
        
    }
}
