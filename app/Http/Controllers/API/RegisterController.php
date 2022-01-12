<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Jobs\WelcomeEmailJob;
use App\Jobs\UserGetRegisteredJob;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{

    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->register($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'email' => ['required', 'email', 'unique:users'],
        ]);
    }

    public function register(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'email' => $data['email'],
        ]);
    }

    protected function registered(Request $request, $user)
    {
        $user->generateToken();

        WelcomeEmailJob::dispatch(request('email'));
        UserGetRegisteredJob::dispatch('martin_nikolov.89@abv.bg');

        return response()->json($user->with(['snippets', 'favoriteSnippets'])->find($user->id)->toArray(), 201);
    }

}
