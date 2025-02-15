<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authentication
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()){
            return redirect()->route('login')->withErrors(
                ['invalid' => 'Silahkan login terlebih dahulu!'
            ]);
        }

        return $next($request);
    }
}
