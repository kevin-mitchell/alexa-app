<?php  namespace Develpr\AlexaApp\Facades;

use Illuminate\Support\Facades\Facade;

class AlexaRouter extends Facade {
	protected static function getFacadeAccessor() {
		return 'alexa.router';
	}
} 