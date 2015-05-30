<?php  namespace Develpr\AlexaApp\Http\Middleware;

use Closure;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken {

	protected $except_urls = [
		'contact/create',
		'contact/update',
    ];

    public function handle($request, Closure $next)
	{
//		$alexaRequest =

		$regex = '#' . implode('|', $this->except_urls) . '#';

		$isAlexaRequest = \Alexa::isAlexaRequest();

	die("HI");

//		$test = $reuqes

//		if ($this->isReading($request) || $this->tokensMatch($request) || preg_match($regex, $request->path()))
//		{
			return $this->addCookieToResponse($request, $next($request));
//		}

//		throw new TokenMismatchException;
	}

}