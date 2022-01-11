<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

class Permission
{

    use response;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
        $user = auth()->user();
        $permission = Route::currentRouteName();


        if(!$user->hasRole('super admin') && !$user->can($permission)){
            return $this->errorResponse('User have not permission for the resource '.$permission,401);
        }
        
        return $next($request);
        
    }
}
