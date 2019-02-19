<?php
namespace App\Http\Middleware;

use Closure;

class ISuserAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::user()->user_type != 'admin') {
            return redirect('login');
        }
        return $next($request);
    }
}


