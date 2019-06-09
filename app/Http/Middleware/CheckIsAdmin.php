<?php

namespace App\Http\Middleware;

use Closure;

class CheckIsAdmin
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
        if (auth()->user()) {
            if (auth()->user()->administrator) {
                return $next($request);
           }
        }
        
        return response('You are not authorized to perform this action', 401);        
    }
}
