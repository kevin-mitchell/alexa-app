<?php

namespace Develpr\AlexaApp\Controllers;

class ProxyController
{
    public function audiofile($audiofile)
    {
        return response(base64_decode($audiofile))
            ->header('Content-Type', 'application/x-mpegurl');
    }
}
