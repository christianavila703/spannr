<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class NoindexStaging
{
    /**
     * The only host that gets the noindex header. Compared for exact equality,
     * so spannr.com, www.spannr.com, app.spannr.com and localhost never match.
     */
    private const STAGING_HOST = 'staging.spannr.com';

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $host = strtolower($request->getHost());
        } catch (SuspiciousOperationException $exception) {
            return $response;
        }

        if ($host === self::STAGING_HOST) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
