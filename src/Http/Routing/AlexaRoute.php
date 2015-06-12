<?php  namespace Develpr\AlexaApp\Http\Routing;

use Develpr\AlexaApp\Http\Routing\Matching\AlexaValidator;
use Illuminate\Routing\Matching\UriValidator;
use \Illuminate\Routing\Route;
use Illuminate\Http\Request;

class AlexaRoute extends Route{

	private $routeType = null;

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
				unset($validators[$key]);
				break;
			}
		}

		$validators[] = new AlexaValidator;

		return $validators;
	}


} 