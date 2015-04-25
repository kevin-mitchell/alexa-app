<?php  namespace Develpr\AlexaApp\Middleware; 

use App\AlexaApp\Request\AlexaRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Http\Request;
use Closure;

class SetupAlexaRequest implements Middleware
{
    const ALEXA_REQUEST_NAMESPACE = 'App\\AlexaApp\\Request\\';

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $app;

    function __construct(Application $app)
    {

        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->app->bind(AlexaRequest::class, function() use ($request) {

            $requestType = array_get(json_decode($request->getContent(), true), 'request.type');

			if($requestType == null)
                $requestType = array_get(json_decode($request->input('content'), true), 'request.type');

            $className = self::ALEXA_REQUEST_NAMESPACE . $requestType;

            if( ! class_exists($className))
            {
                throw new \Exception("This type of request is not supported");
            }

            return new $className($request);

        });

        return $next($request);
    }
} 
