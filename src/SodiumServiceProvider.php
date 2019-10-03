<?php declare(strict_types=1);
namespace Healthlabs\Sodium;

use Healthlabs\Sodium\Services\SodiumService;
use Illuminate\Support\ServiceProvider;

/**
 * The service provider
 */
class SodiumServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app->singleton('sodium', function ($app) {
            $config = $app->make('config')->get('app');
            $key = $config['key'];
            return new SodiumService($key);
        });
    }
    /**
     * {@inheritDoc}
     */
    public function provides()
    {
        return ['sodium'];
    }
}