<?php

namespace App\Providers;

use App\Contracts\Auth\AuthServiceInterface;
use App\Services\Auth\AuthService;
use Illuminate\Support\ServiceProvider;

class BindInterfacesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }
}
