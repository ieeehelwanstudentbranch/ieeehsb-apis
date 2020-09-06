<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Type
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$type)
    {
    $user = Auth::user();
        if($user->type == $type)
        {
            return $next($request);
        }
        else{
            return response()->json(['errors'=>'You Are Not Allowed To See This Page']);

        }
    }
}
