<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function login(Request $request):JsonResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::with(['assignment.division','assignment.section'])->where('username',$credentials['username'])->where('password',md5($credentials['password']))->first();

        if($user && $user->account_status !== 'Active'){

            return response()->json([
                'status' => false,
                'message' => 'Account Deactivated by Administrator'
            ],401);
        }

        if($user){

             $token = $user->createToken($user->hrh_user_id)->plainTextToken;

                return response()->json([
                    'token' => $token,
                    'user' => $user,
                    'status' => true,
                ],200);

        }

        else{
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials, Please Try Again With Correct Dohis Credentials'
            ],401);
        }

    }
}
