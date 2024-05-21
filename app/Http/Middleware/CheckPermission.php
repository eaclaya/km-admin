<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission = null): Response
    {
        Log::info('CheckPermission middleware');
        $resources = session('user_codes');
        Log::info('CheckPermission middleware', ['permission' => $permission]);
        Log::info('CheckPermission middleware', ['resources' => $resources]);
        if (empty($resources) || empty($permission)) {
            return redirect()->back()->withErrors(['error' => 'No tiene permisos para acceder a esta página']);
        }
        if (in_array($permission, $resources)) {
            return $next($request);
        }
        return redirect()->back()->withErrors(['error' => 'No tiene permisos para acceder a esta página']);
    }
}
