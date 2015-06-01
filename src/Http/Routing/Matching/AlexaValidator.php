<?php  namespace Develpr\AlexaApp\Http\Routing\Matching;


use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Matching\ValidatorInterface;
use Illuminate\Routing\Route;

class AlexaValidator implements ValidatorInterface{

	/**
	 * Validate a given rule against a route and request.
	 *
	 * @param  \Illuminate\Routing\Route $route
	 * @param  \Illuminate\Http\Request $request
	 * @return bool
	 */
	public function matches(Route $route, Request $request)
	{
		die("NEED TO DO THIS!");
	}


} 