<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(Request $request):JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);


        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            $token = $user->createToken($user->hrh_user_id)->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'status' => true,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid Credentials, Please Try Again With Correct Dohis Credentials',
        ], 401);


    }
}
