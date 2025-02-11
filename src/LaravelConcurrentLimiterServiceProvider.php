<?php

namespace Patrocle\LaravelConcurrentLimiter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Patrocle\LaravelConcurrentLimiter\Commands\LaravelConcurrentLimiterCommand;

class LaravelConcurrentLimiterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-concurrent-limiter')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_concurrent_limiter_table')
            ->hasCommand(LaravelConcurrentLimiterCommand::class);
    }
}
