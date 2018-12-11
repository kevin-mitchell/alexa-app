<?php

namespace Develpr\AlexaApp\Console\Commands;

use Develpr\AlexaApp\Http\Routing\AlexaRouter;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class AlexaRouteList extends RouteListCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'alexa:route:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered Alexa routes';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Domain', 'Method', 'URI', 'Intent', 'Action', 'Middleware'];

    /**
     * Create a new command instance.
     *
     * @param AlexaRouter $router
     * @return void
     */
    public function __construct(AlexaRouter $router)
    {
        Command::__construct();

        $this->router = $router;
        $this->routes = \Develpr\AlexaApp\Facades\AlexaRouter::getRoutes();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (count($this->routes) === 0) {
            return $this->error("Your application doesn't have any Alexa routes.");
        }

        $this->displayRoutes($this->getRoutes());
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'host'       => $route->domain(),
            'method'     => implode('|', $route->methods()),
            'uri'        => $route->uri,
            'intent'     => preg_replace('/^IntentRequest/S', '', $route->getRouteIntent()),
            'action'     => ltrim($route->getActionName(), '\\'),
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    /**
     * Filter the route by URI and / or name.
     *
     * @param  array  $route
     * @return array|null
     */
    protected function filterRoute(array $route)
    {
        if (($this->option('intent') && ! Str::contains($route['intent'], $this->option('intent'))) ||
            ($this->option('path')   && ! Str::contains($route['uri'],    $this->option('path')))   ||
            ($this->option('method') && ! Str::contains($route['method'], strtoupper($this->option('method'))))) {
            return;
        }

        return $route;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['method', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by method'],

            ['intent', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by intent'],

            ['path',   null, InputOption::VALUE_OPTIONAL, 'Filter the routes by path'],

            ['reverse', 'r', InputOption::VALUE_NONE,     'Reverse the ordering of the routes'],

            ['sort',   null, InputOption::VALUE_OPTIONAL, 'The column (host, method, uri, name, action, middleware) to sort by', 'uri'],
        ];
    }
}
