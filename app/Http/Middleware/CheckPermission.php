<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $resources = $this->checkPermission();
        if (empty($resources) || empty($permission)) {
            return back()->with(['error' => 'No tiene permisos para acceder a esta página, reinicie sesión para recargar los permisos']);
        }
        if (in_array($permission, $resources)) {
            return $next($request);
        }
        return back()->with(['error' => 'No tiene permisos para acceder a esta página']);
    }

    private function checkPermission()
    {
        $resources = session('user_codes');
        if (empty($resources)) {
            $userId = auth()->user()->id;
            $resources = DB::connection('main')
                ->table('users')
                ->join('user_permissions', 'users.role_id', '=', 'user_permissions.role_id')
                ->join('user_resources', 'user_permissions.resource_id', '=', 'user_resources.id')
                ->where('users.id', $userId)
                ->pluck('user_resources.code')
                ->toArray();

            session(['user_codes' => $resources]);
        }
        return $resources;
    }
}
