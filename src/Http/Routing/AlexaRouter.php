<?php  namespace Develpr\AlexaApp\Http\Routing;

use Illuminate\Routing\Router as IlluminateRouter;

class AlexaRouter extends IlluminateRouter{

	private $intentRoutes = [];

	public function intent($uri, $intent, $action)
	{
		$this->intentRoutes[] = $uri;

		$this->addAlexaRoute('POST', $this->prefix($uri), 'IntentRequest' . $intent, $action);

		return $this;
	}

	public function launch($uri, $action)
	{
		$this->intentRoutes[] = $uri;
		$this->addAlexaRoute('POST', $this->prefix($uri), 'LaunchRequest', $action);
	}

	public function sessionEnded($uri, $action)
	{
		$this->intentRoutes[] = $uri;
		$this->addAlexaRoute('POST', $this->prefix($uri), 'SessionEndedRequest', $action);
	}


	/**
	 * Add a route to the underlying route collection.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function addAlexaRoute($methods, $uri, $intent,  $action)
	{
		return $this->routes->add($this->createAlexaRoute($methods, $uri, $intent, $action));
	}

	/**
	 * Create a new Route object.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  mixed   $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function newAlexaRoute($methods, $uri, $intent, $action)
	{
		return (new AlexaRoute($methods, $uri, $intent, $action))->setContainer($this->container)->setRouter($this);
	}


	/**
	 * Create a new route instance.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  mixed   $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function createAlexaRoute($methods, $uri, $intent, $action)
	{
		// If the route is routing to a controller we will parse the route action into
		// an acceptable array format before registering it and creating this route
		// instance itself. We need to build the Closure that will call this out.
		if ($this->actionReferencesController($action))
		{
			$action = $this->convertToControllerAction($action);
		}

		$route = $this->newAlexaRoute(
			$methods, $this->prefix($uri), $intent, $action
		);

		// If we have groups that need to be merged, we will merge them now after this
		// route has already been created and is ready to go. After we're done with
		// the merge we will be ready to return the route back out to the caller.
		if ($this->hasGroupStack())
		{
			$this->mergeGroupAttributesIntoRoute($route);
		}

		$this->addWhereClausesToRoute($route);

		return $route;
	}


	protected function findRoute($request)
	{
		return parent::findRoute($request);
	}

} 