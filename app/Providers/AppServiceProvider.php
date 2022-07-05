<?php

namespace App\Providers;

use App\Models\Universe;
use App\Models\User;
use Gate;
use Godruoyi\Snowflake\RandomSequenceResolver;
use Godruoyi\Snowflake\Snowflake;
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
        $this->app->singleton('snowflake', function () {
            return (new Snowflake(
                config('snowflake.data_center'),
                config('snowflake.worker_node')
            )
            )
                ->setStartTimeStamp(strtotime('2022-05-01') * 1000)
                ->setSequenceResolver(new RandomSequenceResolver());
        });
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

        Gate::define('owns-universe', function (?User $user, ?Universe $universe = null) {
            return (int)$universe?->user_id === (int)$user?->id;
        });
    }
}
