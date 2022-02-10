<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class loginController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => ['required', 'email', 'string', 'exists:users,email'],
            'password' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                throw ValidationException::withMessages([
                    'email' => ['ایمیل وارد شده صحیح نمی باشد.'],
                ]);
            }

            // Checking Password
            if (Hash::check($request->password, $user->password)) {
                // Success Response
                return response()->json([
                    'status' => true,
                    'Token_Id' => $user->createToken('token_base_name')->plainTextToken,
                ], 200);
            }
        }

        // Error Response
        return response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ], 401);
    }
}
