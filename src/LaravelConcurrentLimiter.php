<?php

namespace Patrocle\LaravelConcurrentLimiter;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class LaravelConcurrentLimiter
{
    /**
     * Handle an incoming request.
     *
     * Parameters:
     *  - $maxParallel: Maximum concurrent requests allowed.
     *  - $maxWaitTime: Maximum time (in seconds) to wait for a slot.
     *  - $prefix: Optional prefix for the cache key.
     *
     * Usage example:
     *   Route::middleware('concurrent.limit:10,30,api')->group( ... );
     *
     * @param  int|string  $maxParallel
     * @param  int|string  $maxWaitTime
     * @param  string  $prefix
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxParallel = 10, $maxWaitTime = 30, $prefix = '')
    {
        $maxParallel = (int) $maxParallel;
        $maxWaitTime = (int) $maxWaitTime;

        // Build a unique key based on a prefix plus the request signature.
        $key = $prefix.$this->resolveRequestSignature($request);

        // Record the start time (using microtime for finer granularity).
        $startTime = microtime(true);

        // Atomically increment the counter for this key.
        $current = Cache::increment($key);

        // Optionally set a timer on the key to avoid stale counters.
        if (! Cache::has($key.':timer')) {
            Cache::put($key.':timer', time(), $maxWaitTime + 5);
        }

        // Loop until the current count is within the allowed maximum.
        while ($current > $maxParallel) {
            // If we've waited longer than allowed, decrement the counter and return an error.
            if ((microtime(true) - $startTime) >= $maxWaitTime) {
                Cache::decrement($key);

                return response()->json([
                    'message' => 'Too many concurrent requests. Please try again later.',
                ], Response::HTTP_SERVICE_UNAVAILABLE);
            }
            // Wait 100 milliseconds before checking again.
            usleep(100_000);
            $current = Cache::get($key) ?? 0;
        }

        try {
            return $next($request);
        } finally {
            // Always decrement the counter even if an exception occurs.
            Cache::decrement($key);
        }
    }

    /**
     * Resolve a unique signature for the request.
     *
     * Uses the authenticated user’s ID if available, otherwise falls back to the IP address.
     *
     *
     * @throws \RuntimeException
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        } elseif ($ip = $request->ip()) {
            return sha1($ip);
        }

        throw new RuntimeException('Unable to generate the request signature. No user or IP available.');
    }

    /**
     * Helper to create the middleware definition.
     *
     * This static method allows usage like:
     *   ConcurrentLimiter::with(10, 30, 'api')
     *
     * @param  int|string  $maxParallel
     * @param  int|string  $maxWaitTime
     * @param  string  $prefix
     * @return string
     */
    public static function with($maxParallel = 10, $maxWaitTime = 30, $prefix = '')
    {
        return static::class.':'.implode(',', func_get_args());
    }
}
