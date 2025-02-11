<?php

namespace Patrocle\LaravelConcurrentLimiter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelConcurrentLimiterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-concurrent-limiter')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('concurrent.limit', LaravelConcurrentLimiter::class);
    }
}
