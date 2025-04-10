<?php

namespace App\Providers;

use App\Models\Main\PersonalAccessToken;
use App\Models\SetupMenu;
use App\Repositories\ReportProcessRepository;
use App\Repositories\SpecialNegotiationsRepository;
use App\Services\AdminlteMenuFilterSource;
use App\Services\FilesServices;
use App\Services\ReportProcessServices;
use App\Services\SpecialNegotiationsService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Laravel\Sanctum\Sanctum;

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
        // binds repositories of modules

        $this->app->bind(ReportProcessRepository::class, function ($app) {
            return new ReportProcessRepository();
        });
        $this->app->bind(SpecialNegotiationsRepository::class, function ($app) {
            return new SpecialNegotiationsRepository();
        });

        // singletons services of modules

        $this->app->singleton(ReportProcessServices::class, function ($app) {
            return new ReportProcessServices(new ReportProcessRepository(), new FilesServices());
        });
        $this->app->singleton(SpecialNegotiationsService::class, function ($app) {
            return new SpecialNegotiationsService(new SpecialNegotiationsRepository());
        });

        // singletons services of commons

        $this->app->singleton(FilesServices::class, function ($app) {
            return new FilesServices();
        });
        $this->app->singleton(AdminlteMenuFilterSource::class, function ($app) {
            return new AdminlteMenuFilterSource();
        });

        // first chargers of commons

        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            if (! session()->has('menu')) {
                $setupMenu = SetupMenu::query()
                    ->where('supra_menu_id', null)
                    ->orWhere('supra_menu_id', 0)
                    ->with('subItems')
                    ->get();
                $menu = [];
                foreach ($setupMenu as $item) {
                    $menu[] = $this->returnItems($item);
                }
                session()->put('menu', $menu);
            } else {
                $menu = session()->get('menu');
            }
            $event->menu->add(...$menu);
        });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }

    public function returnItems($item, $ml = 0): array
    {
        $itemArr = $item->toArray();

        $menu = array_filter($itemArr, function ($value) {
            return ! is_null($value);
        });
        if ($ml > 0) {
            $menu['classes'] = 'ml-'.$ml;
        }
        unset($menu['supra_menu_id']);
        unset($menu['sub_items']);
        unset($menu['id']);

        if (isset($item->subItems) && count($item->subItems) > 0) {
            $menu['submenu'] = [];
            $ml = $ml + 3;
            foreach ($item->subItems as $subItem) {
                $menu['submenu'][] = $this->returnItems($subItem, $ml);
            }
        }

        return $menu;
    }
}
