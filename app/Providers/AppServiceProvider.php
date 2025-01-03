<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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
            $link = Link::where('user_id', auth()->id())->first();
            $logoBase64 = $link ? $link->image_base64 : '';
            $view->with('logoBase64', $logoBase64);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
