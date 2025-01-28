<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Link;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Compartilhar a variável logoBase64 com todas as views
        View::composer('*', function ($view) {
            /* $link = Link::where('user_id', auth()->id())->first(); */

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if(config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
