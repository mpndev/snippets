<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class GoogleLoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->only([
            'create',
            'store',
        ]);
    }

    public function create()
    {
        $redirect_url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect_url' => $redirect_url], 200);
    }

    public function store()
    {
        $google_user = Socialite::driver('google')->stateless()->user();
        if (!$google_user || !$google_user->email || !$google_user->id) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $user = User::where('email', $google_user->email)->first();
        if (!$user) {
            $user = $this->createUserFromGoogle($google_user);
        }
        else if ($user->google_id == null) {
            $user->google_id = $google_user->id;
            $user->save();
        }

        if ($user->api_token === null) {
            $user->generateToken();
        }

        return response()->json($user->with(['snippets', 'favoriteSnippets'])->find($user->id)->toArray(), 200);
    }

    private function createUserFromGoogle($google_user)
    {
        return User::create([
            'name' => $google_user->name,
            'google_id' => $google_user->id,
            'email' => $google_user->email,
        ]);
    }

}
