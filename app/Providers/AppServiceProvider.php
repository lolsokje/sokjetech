<?php

namespace App\Providers;

use App\Models\Universe;
use App\Models\User;
use Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
        Model::unguard();

        Model::preventLazyLoading(!$this->app->isProduction());

        Gate::define('owns-universe', function (?User $user, Universe $universe) {
            return $universe->user_id === $user?->id;
        });
    }
}
