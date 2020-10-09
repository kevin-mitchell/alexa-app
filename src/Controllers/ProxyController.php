<?php

namespace Develpr\AlexaApp\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Routing\Controller;

class ProxyController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function audiofile($audiofile)
    {
        return response(base64_decode($audiofile))
            ->header('Content-Type', 'application/x-mpegurl');
    }
}
