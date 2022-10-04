<?php

namespace MTGofa\QueryCache\Providers;

use Illuminate\Support\ServiceProvider;
use MTGofa\QueryCache\Commands\PerfectlyCacheClearCommand;
use MTGofa\QueryCache\Commands\PerfectlyCacheListCommand;
use MTGofa\QueryCache\Events\ModelEvents;
use MTGofa\QueryCache\Extensions\PerfectlyStore;
use MTGofa\QueryCache\Listeners\ModelDispactEventListener;
use MTGofa\QueryCache\PerfectlyCache;

class QueryCacheServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $this->registerSingletons();
        $this->registerAlias();
        $this->publish();
        $this->registerCommands();
    }

    public function register()
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'perfectly-cache');
    }

    /**
     * Register singletons to app
     */
    protected function registerSingletons()
    {
        $this->app->singleton(PerfectlyCache::class);
    }

    /**
     * Register alias to app
     */
    protected function registerAlias()
    {
        $this->app->alias(PerfectlyCache::class, "perfectly-cache");
    }

    /**
     * Publish vendors
     */
    protected function publish()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('perfectly-cache.php')
        ]);
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PerfectlyCacheClearCommand::class,
                PerfectlyCacheListCommand::class
            ]);
        }
    }
}
