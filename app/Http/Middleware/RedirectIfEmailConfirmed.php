<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfEmailConfirmed
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
        if(!$request->user()->confirmed){
            return redirect()->route('threads.index')
                             ->with('flash', 'You must first confirm your email address.');
        }

        return $next($request);
    }
}
