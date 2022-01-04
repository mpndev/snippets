<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordForgetController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->only([
            'store',
        ]);
    }

    public function store()
    {
        request()->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(request()->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['status' => $status], 200);
        }

        throw ValidationException::withMessages([
            'email' => [$status],
        ]);
    }
}
