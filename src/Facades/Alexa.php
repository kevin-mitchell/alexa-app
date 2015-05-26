<?php namespace Develpr\AlexaApp\Facades;

use Illuminate\Support\Facades\Facade;

class Alexa extends Facade{

	protected static function getFacadeAccessor() {
		return 'alexa';
	}

} 