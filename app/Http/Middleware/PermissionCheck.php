<?php

namespace App\Http\Middleware;

use Closure;
use App\Action\Permission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string|array $role): Response
    {
        /**
         * Show unauthorized error if user doesnt have permission
         */
        abort_if(!Permission::has($role), 401);

        return $next($request);
    }
}
