<?php

namespace App\Providers;

use App\Contracts\Auth\AuthServiceInterface;
use App\Contracts\Student\StudentServiceInterface;
use App\Contracts\Subject\SubjectServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Student\StudentService;
use App\Services\Subject\SubjectService;
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
        $this->app->bind(StudentServiceInterface::class, StudentService::class);
        $this->app->bind(SubjectServiceInterface::class, SubjectService::class);
    }
}
