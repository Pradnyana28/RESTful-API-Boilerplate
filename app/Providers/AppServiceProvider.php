<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(UrlGenerator $url)
    {
        Schema::defaultStringLength(191);

        if ( env('REDIRECT_HTTPS') ) {
            $url->formatScheme('https');
        }
    }

    public function register()
    {
        if ( env('REDIRECT_HTTPS') ) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
