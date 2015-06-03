<?php  namespace Develpr\AlexaApp\Http\Middleware;

use Develpr\AlexaApp\Http\Routing\AlexaRouter;
use Closure;
use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request as IlluminateRequest;


class Request {
	/**
	 * Application instance.
	 *
	 * @var \Illuminate\Contracts\Foundation\Application
	 */
	protected $app;

	/**
	 * Router instance.
	 *
	 * @var AlexaRouter
	 */
	protected $router;

	/**
	 * HTTP validator instance.
	 *
	 * @var \Dingo\Api\Http\Validator
	 */
//	protected $validator;

	/**
	 * Array of middleware.
	 *
	 * @var array
	 */
	protected $middleware;
	/**
	 * @var \Develpr\AlexaApp\Request\AlexaRequest
	 */
	private $alexaRequest;

	/**
	 * @param Application $app
	 * @param AlexaRouter $router
	 * @param AlexaRequest $alexaRequest
	 * @param array $middleware
	 */
	public function __construct(Application $app, AlexaRouter $router, AlexaRequest $alexaRequest, array $middleware)
	{
		$this->app = $app;
		$this->router = $router;
		$this->middleware = $middleware;
		$this->alexaRequest = $alexaRequest;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$test = "HI";

//		if ($this->validator->validateRequest($request)) {
		if ($this->alexaRequest->isAlexaRequest()) {
			return $this->sendRequestThroughRouter($request);
		}

		return $next($request);
	}

	/**
	 * Send the request through the Dingo router.
	 *
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return \Dingo\Api\Http\Response
	 */
	protected function sendRequestThroughRouter( $request)
	{
		$this->app->instance('request', $request);

		return (new Pipeline($this->app))->send($request)->through($this->middleware)->then(function ($request) {
			return $this->router->dispatch($request);
		});

	}
} 