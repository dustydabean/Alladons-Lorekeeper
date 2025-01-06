<?php

namespace App\Providers;

use App\Models\Theme;
use App\Providers\Socialite\ToyhouseProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() {
        //
        Schema::defaultStringLength(191);
        Paginator::defaultView('layouts._pagination');
        Paginator::defaultSimpleView('layouts._simple-pagination');

        view()->composer('*', function () {
            $theme = Auth::user()->theme ?? Theme::where('is_default', true)->first() ?? null;
            $conditionalTheme = null;
            if (class_exists('\App\Models\Weather\WeatherSeason')) {
                $conditionalTheme = Theme::where('link_type', 'season')->where('link_id', Settings::get('site_season'))->first() ??
                Theme::where('link_type', 'weather')->where('link_id', Settings::get('site_weather'))->first() ??
                $theme;
            }
            $decoratorTheme = Auth::user()->decoratorTheme ?? null;
            View::share('theme', $theme);
            View::share('conditionalTheme', $conditionalTheme);
            View::share('decoratorTheme', $decoratorTheme);
        });

        /*
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path'     => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        $this->bootToyhouseSocialite();
    }

    /**
     * Boot Toyhouse Socialite provider.
     */
    private function bootToyhouseSocialite() {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'toyhouse',
            function ($app) use ($socialite) {
                $config = $app['config']['services.toyhouse'];

                return $socialite->buildProvider(ToyhouseProvider::class, $config);
            }
        );
    }
}
