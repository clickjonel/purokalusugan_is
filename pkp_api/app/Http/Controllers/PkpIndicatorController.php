<?php

namespace App\Http\Controllers;
use App\Models\Pkp_indicator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpIndicatorController extends Controller
{
    // create
    public function createIndicator(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'program_id' => 'required|integer',
            'indicator_code' => 'string',
            'indicator_name' => 'string',
            'indicator_description' => 'string',
            'indicator_status' => 'integer',
            'indicator_scope' => 'integer'            
        ]);
        $pkpIndicator=Pkp_indicator::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$pkpIndicator
        ], 201);
    }

    // update


    // list


    //delete
}


