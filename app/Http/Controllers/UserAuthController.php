<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserAuthController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $credentials['blocked'] = 0;
        // get user
        $userData = [];
        $getUserData = User::with('permission')->where('email', $credentials['email'])->first();
        if ($getUserData) {
            $userData = [
                'user' => [
                    'id_user' => $getUserData->id_user,
                    'id_group' => $getUserData->id_group,
                    'email' => $getUserData->email,
                    'name' => $getUserData->name,
                    'permission' => $getUserData->permission->name
                ]
            ];
        }

        if (! $token = auth()->claims($userData)->attempt($credentials)) {
            return $this->errorResponse('Invalid user credentials!', 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return $this->showOne(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return $this->showMessage('Successfully logged out');
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120
        ]);
    }
}
