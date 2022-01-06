<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class FacebookLoginController extends Controller
{
    use AuthenticatesUsers;

    public function redirectToProvider()
    {
        $redirect_url = Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect_url' => $redirect_url], 200);
    }

    public function handleProviderCallback()
    {
        $facebook_user = Socialite::driver('facebook')->stateless()->user();
        if (!$facebook_user || !$facebook_user->name || !$facebook_user->id) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $user = User::where('name', $facebook_user->name)->first();
        if (!$user) {
            $user = $this->createUserFromFacebook($facebook_user);
        }
        else if ($user->facebook_id == null) {
            $user->facebook_id = $facebook_user->id;
            $user->save();
        }

        if ($user->api_token === null) {
            $user->generateToken();
        }

        return response()->json($user->with(['snippets', 'favoriteSnippets'])->find($user->id)->toArray(), 200);
    }

    private function createUserFromFacebook($facebook_user)
    {
        return User::create([
            'name' => $facebook_user->name,
            'facebook_id' => $facebook_user->id,
        ]);
    }

}
