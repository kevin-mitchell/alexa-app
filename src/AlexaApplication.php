<?php

namespace Develpr\AlexaApp;

use Laravel\Lumen\Application;

class AlexaApplication extends Application
{
    private $intentRoutes = [];

    public function intent($uri, $intent, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addRoute('INTENT', '*'.$intent, $action);

        return $this;
    }

    public function launch($uri, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addRoute('INTENT', '**'.'LAUNCH_REQUEST', $action);
    }

    public function sessionEnded($uri, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addRoute('INTENT', '**'.'SESSION_ENDED_REQUEST', $action);
    }

    /**
     * @return string
     */
    protected function getMethod()
    {
        $method = parent::getMethod();

        if ($this->isRouteWithIntent()) {
            $method = 'INTENT';
        }

        return $method;
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        $pathInfo = parent::getPathInfo();

        if ($this->isRouteWithIntent()) {
            $intentRelated = $this->getIntentFromRequest();
            $pathInfo = '/'.$intentRelated;
        }

        return $pathInfo;
    }

    /**
     * Has a route been registered to this path with an Intent?
     *
     * @return bool
     */
    private function isRouteWithIntent()
    {
        return parent::getMethod() === 'POST' && in_array(parent::getPathInfo(), $this->intentRoutes);
    }

    /**
     * @return string
     */
    private function getIntentFromRequest()
    {
        $request = $this->make('request');

        $data = json_decode($request->getContent(), true);

        switch (array_get($data, 'request.type')) {
            case 'SessionEndedRequest':
                return '**'.'SESSION_ENDED_REQUEST';
            case 'LaunchRequest':
                return '**'.'LAUNCH_REQUEST';
            case 'IntentRequest':
                return '*'.ltrim(array_get($data, 'request.intent.name'));
        }
    }
}
