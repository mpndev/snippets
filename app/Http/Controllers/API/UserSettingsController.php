<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;

class UserSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['update']);
    }

    public function update(User $user)
    {
        $user->settings = request('settings');
        $user->save();

        return response()->json($user->toArray(), 202);
    }
}
