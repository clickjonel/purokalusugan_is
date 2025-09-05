<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class GeoJsonController extends Controller
{
    public function fetch()
    {
        $geojsonDir = public_path('geojson/');

        $provinces = [
            ['name' => 'Cordillera Administrative Region', 'id' => 'car', 'population' => 1800000],
            ['name' => 'Abra', 'id' => 'abra', 'population' => 250000],
            ['name' => 'Apayao', 'id' => 'apayao', 'population' => 125000],
            ['name' => 'Benguet', 'id' => 'benguet', 'population' => 450000],
            ['name' => 'Ifugao', 'id' => 'ifugao', 'population' => 200000],
            ['name' => 'Kalinga', 'id' => 'kalinga', 'population' => 220000],
            ['name' => 'Mountain Province', 'id' => 'mtprovince', 'population' => 160000],
            ['name' => 'Baguio City', 'id' => 'baguio', 'population' => 345000],
        ];

        $data = [];

        foreach ($provinces as $prov) {
            $filePath = $geojsonDir . $prov['id'] . '.json';

            if (File::exists($filePath)) {
                $geojson = File::get($filePath);
                $gj = json_decode($geojson, true);

                $gj['properties'] = [
                    'name' => $prov['name'],
                    'population' => $prov['population'],
                ];

                $data[] = $gj;
            }
        }

        return response()->json($data);
    }
}