<?php

namespace App\Http\Controllers;

use App\Http\Requests\HRH\CreateHrhRequest;
use App\Models\Hrh;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrhController extends Controller
{
    public function createHrhUser(CreateHrhRequest $request):JsonResponse
    {
        $validatedData=$request->validated();
        $hrh = Hrh::create($validatedData);

         return response()->json([
            'message' => 'Created HRH successfully',
            'data'=>$hrh
        ], 201);
    }
    
}
