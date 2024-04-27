<?php

namespace App\Modules\Order;

use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }
}
