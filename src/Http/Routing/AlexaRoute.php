<?php  namespace Develpr\AlexaApp\Http\Routing;

use Develpr\AlexaApp\Http\Routing\Matching\AlexaValidator;
use Illuminate\Routing\Matching\UriValidator;
use \Illuminate\Routing\Route;
use Illuminate\Http\Request;

class AlexaRoute extends Route{

	private $routeIntent = null;

	public function __construct($methods, $uri, $intent, $action)
	{
		parent::__construct($methods, $uri, $action);

		$this->routeIntent = $intent;
	}

	public function getRouteIntent()
	{
		return $this->routeIntent;
	}


	/**
	 * Set the router instance on the route.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return $this
	 */
	public function setRouter(\Illuminate\Routing\Router $router)
	{
		$this->router = $router;

		return $this;
	}


	/**
	 * Determine if the route matches given request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  bool  $includingMethod
	 * @return bool
	 */
//	public function matches(Request $request, $includingMethod = true){
//
//	}

	/**
	 * Get the route validators for the instance.
	 *
	 * @return array
	 */
	public static function getValidators()
	{
		$validators = parent::getValidators();

		foreach($validators as $key => $validator)
		{
			if($validator instanceof UriValidator){
				//unset($validators[$key]);
				break;
			}
		}

		$validators[] = new AlexaValidator;

		return $validators;
	}

	public function getUri()
	{
		return parent::getUri() . $this->getRouteIntent();
	}


} 