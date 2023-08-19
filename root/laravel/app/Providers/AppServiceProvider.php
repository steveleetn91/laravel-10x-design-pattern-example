<?php

namespace App\Providers;

use App\Services\Users\UserServiceFactory;
use App\Services\Users\UserServiceFactoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
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
        //
        $this->app->bind(UserServiceFactoryInterface::class, UserServiceFactory::class);
    }
}
