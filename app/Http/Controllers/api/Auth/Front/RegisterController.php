<?php

namespace App\Http\Controllers\api\Auth\Front;

use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{

    use RegistersUsers;

    protected $redirectTo = '';

    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if (!$validator->fails()) {
            $user = $this->create($request->all());

            $token = $this->auth::attempt($request->only('email', 'password'));

            return response()->json([
                'success' => true,
                'data' => $user,
                'token' => $token
            ]);
        }
        return response()->json([
            'success' => false,
            'status' => 422,
            'errors' => $validator->errors()
        ]);
    }

    protected function validator(array $data) : \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    protected function create(array $data) :User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
