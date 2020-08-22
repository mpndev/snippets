<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class PreferLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->headers->has('Langcode')) {
            App::setLocale($request->header('Langcode'));
        }

        return $next($request);
    }
}
