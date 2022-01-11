<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;

class IsProfileOwnerOrCanManageUsers
{
    public $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->auth->id() == $request->segment(3) || $this->auth->user()->hasAbility('manage_users')) {
            return $next($request);
        }
        else {
            throw new AuthorizationException();
        }

    }

}
