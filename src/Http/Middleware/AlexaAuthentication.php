<?php namespace Develpr\AlexaApp\Http\Middleware;

use App\Http\Requests\Request;
use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Illuminate\Contracts\Routing\Middleware;
use Closure;

class AlexaAuthentication implements Middleware {

    public function handle($request, Closure $next)
    {
        /** @var AmazonEchoDevice $device */
        $device = \Alexa::device();

        if( ! $device ){
                return response('Unauthorized.', 401);
        }

        return $next($request);

    }
}
