<?php

namespace Develpr\AlexaApp\Http\Middleware;

use Closure;
use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Illuminate\Contracts\Routing\Middleware;

class AlexaAuthentication implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function handle($request, Closure $next)
    {
        /** @var AmazonEchoDevice $device */
        $device = \Alexa::device();

        if (!$device ) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
