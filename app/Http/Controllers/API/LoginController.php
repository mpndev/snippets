<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'destroy',
        ]);
        $this->middleware('guest')->only([
            'create',
            'store',
        ]);
    }

    public function store(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();

            if ($user->api_token === null) {
                $user->generateToken();
            }

            return response()->json($user->with(['snippets', 'favoriteSnippets'])->find($user->id)->toArray(), 200);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function destroy(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['Successful logged out.'], 200);
    }

    public function username()
    {
        return 'name';
    }
}
