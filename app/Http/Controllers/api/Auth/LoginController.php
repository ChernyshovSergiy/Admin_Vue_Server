<?php

namespace App\Http\Controllers\api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct() {
    }

    public function me()
    {
        $user = auth()->user();

        return response()->json(compact('user'));
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        $user = User::where('email', request('email'))->get()->first();
//        dd($user->status);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credential.'], 401);
        }

        $user = $request->user();

        return response()->json(compact('token',  'user'));
    }

    public function refresh()
    {
        $token = auth()->refresh();

        return response()->json(compact('token'));
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
