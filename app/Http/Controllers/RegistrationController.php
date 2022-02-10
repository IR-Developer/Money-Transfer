<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{

    public function register(Request $request)
    {
        $rules = [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'unique:users,email', 'regex:/^(([a-zA-Z0-9])*(\.|_){0,1}([a-zA-Z0-9])+)+@[a-zA-Z0-9]+(\.[a-zA-Z0-9]{2,3}){1,2}$/i', 'max:255'],
            'shomare_sheba' => ['required', 'string', 'unique:users,shomare_sheba', 'regex:/^ir[0-9]{24}$/i', 'size:26'],
            'shomare_hesab' => ['required', 'string', 'unique:users,shomare_hesab', 'regex:/^[0-9]{11}$/i', 'size:11'],
            'password' => ['required', 'string', 'min:8'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {

            $info = [
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'shomare_sheba' => $request->shomare_sheba,
                'shomare_hesab' => $request->shomare_hesab,
            ];

            if($newUser = User::create($info)){
                // Success Response
                return response()->json([
                    'status' => true,
                    'Token_Id' => $newUser->createToken('token_base_name')->plainTextToken,
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
