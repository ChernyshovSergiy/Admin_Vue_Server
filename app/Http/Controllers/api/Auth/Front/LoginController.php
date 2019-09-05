<?php

namespace App\Http\Controllers\api\Auth\Front;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '';

    protected $auth;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return response()->json([
                'success' => false,
                'errors' => [
                    "You've been locked out"
                ]
            ]);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        try {
            if (!$token = $this->auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'text'=> $this->auth::attempt($request->only('email', 'password')),
                    'text2'=> $token,
                    'success' => false,
                    'errors' => [
                        'email' => [
                            'Invalid email or password'
                        ]
                    ]
                ], 422);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'email' => [
                        'Exception Invalid email or password'
                    ]
                ]
            ], 422);
        }
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'token' => $token
        ], 200);
    }
}
