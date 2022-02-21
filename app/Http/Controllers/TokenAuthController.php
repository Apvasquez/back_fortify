<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenAuthController extends Controller
{
    private $device_name = 'postman';
    public function store()
    {
        $request = request();

        $request->validate(
            [
                'email'       => 'required|email',
                'password'    => 'required',
                
            ]
        );
      

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(
                [
                    'email' => ['The provided credentials are incorrect.'],
                ]
            );
        }

        return response()->json(
            ['user' => new UserResource($user), 'token' => $user->createToken($this->device_name)->plainTextToken]
        );
    }

    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json();
    }
}
