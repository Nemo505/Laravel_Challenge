<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
         ]);

        $credentials = $request->only('email', 'password');

        if ($validator->fails()) {
            return apiResponse('Validation Error', $validator->errors()->all(), 422);

        }else{

            $user = User::where('email', $request->email)->first();
                if (!$token = $user) {
                    return apiResponse("Unauthorized", $token, 401);
                }else{
                    $token = Auth::attempt($credentials);
                    if ($token == false) {
                        return apiResponse("Password Does Not Match", $token, 401);
                    } else {
                        $user_token = [
                            'user' => $user,
                            'token' => $user->createToken('User-Token')->plainTextToken,
                        ];
                        return apiResponse("User Logged In Successfully", $user_token, 200);
                    }

                }
        }
    }
}
