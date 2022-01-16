<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserDarkmodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['update']);
    }

    public function update(User $user)
    {
        $user->darkmod = request()->has('darkmod') ? request('darkmod') : false;
        $user->save();

        return response()->json($user->toArray(), 202);
    }
}
