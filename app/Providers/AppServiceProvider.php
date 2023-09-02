<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        if(env('ENFORCE_SSL', false)) {
            $url->forceScheme('https');
        }

        Inertia::share('errors', function () {
            if (Session::get('errors')) {
                $bags = [];

                foreach (Session::get('errors')->getBags() as $bag => $error) {
                   $bags[$bag] = $error->getMessages();
                }

                return $bags;
            }

            return (object)[];
        });
    }
}
