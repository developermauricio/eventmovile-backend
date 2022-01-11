<?php

namespace App\Http\Middleware;

use Closure;

class cxSummit
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
        $username = null;
        $headerss = $request->headers->all();
        if(isset($headerss['authorization'][0]))
            $username = $headerss['authorization'][0];
        
        if($username == env('CXSUMMIT')){
            return $next($request);
        }
        
        $response = [
            'message' => 'Unauthorized',
        ];

        return response()->json($response, 401);
    }
}
