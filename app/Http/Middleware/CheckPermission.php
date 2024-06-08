<?php

namespace App\Http\Middleware;

use App\Models\SetupMenu;
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
        $resources = $this->checkResources();
        if (empty($resources)) {
            return back()->with(['error' => 'No tiene permisos para acceder a esta página, reinicie sesión para recargar los permisos']);
        }
        if(isset($permission)){
            if (in_array($permission, $resources)) {
                return $next($request);
            }
        }
        $currentUrl = '/'.request()->path();
        $menu = $this->checkMenu();
        $permissionMenu = $this->checkPermissionMenu($menu, $currentUrl);
        if(isset($permissionMenu)){
            if (in_array($permissionMenu, $resources)) {
                return $next($request);
            }
        }
        return back()->with(['error' => 'No tiene permisos para acceder a esta página']);
    }

    private function checkResources()
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

    private function checkMenu(){
        $menu = session('menu');
        if (empty($menu)) {
            $setupMenu = SetupMenu::query()
                ->where('supra_menu_id', null)
                ->orWhere('supra_menu_id', 0)
                ->with('subItems')
                ->get();
            $menu = [];
            foreach ($setupMenu as $item){
                $menu[] = $this->returnItems($item);
            }
            session(['menu' => $menu]);
        }
        return $menu;
    }

    private function checkPermissionMenu($array, $url)
    {
        foreach ($array as $item) {
            if (is_array($item)) {
                if (isset($item['url']) && $item['url'] == $url) {
                    return isset($item['can']) ? $item['can'] : null;
                }
                if(isset($item['submenu']) && count($item['submenu']) > 0){
                    $result = $this->checkPermissionMenu($item['submenu'], $url);
                    if(isset($result)){
                        return $result;
                    }
                }
            }
        }
        return null;
    }
    public function returnItems($item, $ml = 0): array{
        $itemArr = $item->toArray();
        $menu = array_filter($itemArr, function ($value) {
            return !is_null($value);
        });
        if ($ml > 0){
            $menu['classes'] = 'ml-'.$ml;
        }
        unset($menu['supra_menu_id']);
        unset($menu['sub_items']);
        unset($menu['id']);

        if (isset($item->subItems) && count($item->subItems) > 0){
            $menu['submenu'] = [];
            $ml = $ml + 3;
            foreach ($item->subItems as $subItem){

                $menu['submenu'][] = $this->returnItems($subItem,$ml);
            }
        }
        return $menu;
    }
}
