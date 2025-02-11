<?php

namespace Patrocle\LaravelConcurrentLimiter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Patrocle\LaravelConcurrentLimiter\LaravelConcurrentLimiter
 */
class LaravelConcurrentLimiter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Patrocle\LaravelConcurrentLimiter\LaravelConcurrentLimiter::class;
    }
}
