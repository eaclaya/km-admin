<?php

namespace App\Providers;

use App\Models\SetupMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // chequear si existe session sino lo guardamos
            if (!session()->has('menu')) {
                $setupMenu = SetupMenu::query()
                    ->where('supra_menu_id', null)
                    ->orWhere('supra_menu_id', 0)
                    ->with('subItems')
                    ->get();
                $menu = [];
                foreach ($setupMenu as $item){
                    $menu[] = $this->returnItems($item);
                }
                session()->put('menu', $menu);
            }else{
                $menu = session()->get('menu');
            }
            $event->menu->add(...$menu);
        });
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
