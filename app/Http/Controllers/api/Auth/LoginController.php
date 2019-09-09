<?php

namespace App\Http\Controllers\api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
        if (is_object($user) && (int)$user->status[7] === 1){
                if (!$token = auth('api')->attempt($credentials)) {
                    return response()->json(['message' => 'Invalid login credential.'], 401);
                }

                $user = $request->user();

                return response()->json(compact('token',  'user'));
        }
        LaravelLocalization::setLocale($request->get('cLang'));
        return response()->json(['message' => Lang::get('messages.we_are_very_sorry')], 403);
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
