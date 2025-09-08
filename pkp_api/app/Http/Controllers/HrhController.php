<?php

namespace App\Http\Controllers;

use App\HRHTrait;
use App\Http\Requests\HRH\CreateHrhRequest;
use App\Models\Hrh;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrhController extends Controller
{
    use HRHTrait;

    public function createHrhUser(CreateHrhRequest $request):JsonResponse
    {
        $validatedData=$request->validated();
        $hrh = Hrh::create([
            'user_code' => "DOH".uniqid(),
            'image' => null,
            'username' => $this->formatUsername($validatedData['first_name'], $validatedData['middle_name'] ?? null, $validatedData['last_name']),
            'password' => bcrypt('12345'),
            'first_name' => $validatedData['first_name'],
            'middle_name' => $validatedData['middle_name'] ?? null,
            'last_name' => $validatedData['last_name'],
            'suffix' => $validatedData['suffix'] ?? null,
            'nickname' => $validatedData['nickname'],
            'user_level' => $validatedData['user_level'],
            'account_status' => 'unassigned',
        ]);

         return response()->json([
            'message' => 'Created HRH successfully',
            'data'=>$hrh
        ], 201);
    }

    public function getHrhList(Request $request):JsonResponse
    {
        $list = Hrh::orderBy('hrh_user_id','DESC')->get();

         return response()->json([
            'message' => 'Fetched HRH List successfully',
            'list'=>$list
        ], 200);
    }

}
