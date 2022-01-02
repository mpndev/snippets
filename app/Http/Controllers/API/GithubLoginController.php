<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class GithubLoginController extends Controller
{
    use AuthenticatesUsers;

    public function redirectToProvider()
    {
        $redirect_url = Socialite::driver('github')->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect_url' => $redirect_url], 200);
    }

    public function handleProviderCallback()
    {
        $github_user = Socialite::driver('github')->stateless()->user();
        if (!$github_user || !$github_user->nickname || !$github_user->id) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ]);
        }

        $user = User::where('name', $github_user->nickname)->first();
        if (!$user) {
            $user = $this->createUserFromGithub($github_user);
        }
        else if ($user->github_id == null) {
            $user->github_id = $github_user->id;
            $user->save();
        }

        if ($user->api_token === null) {
            $user->generateToken();
        }

        return response()->json($user->with(['snippets', 'favoriteSnippets'])->find($user->id)->toArray(), 200);
    }

    private function createUserFromGithub($github_user)
    {
        return User::create([
            'name' => $github_user->nickname,
            'github_id' => $github_user->id,
        ]);
    }

}
