<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('can_manage_users')->only(['index']);
        $this->middleware('is_profile_owner_or_can_manage_users')->only(['show', 'update', 'destroy']);
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users, 206);
    }

    public function show(User $user)
    {
        $user->load(['snippets']);
        return response()->json($user->toArray());
    }

    public function update(User $user)
    {
        request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = request()->name;
        if (mb_strlen(request()['password']) > 0) {
            $user->password = Hash::make(request()->password);
        }
        $user->save();

        $user->load(['snippets']);
        return response()->json($user->toArray(), 200);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null,204);
    }
}
