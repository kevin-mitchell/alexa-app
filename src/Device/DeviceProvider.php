<?php  namespace Develpr\AlexaApp\Device;


use Develpr\AlexaApp\Contracts\AmazonEchoDevice;

interface DeviceProvider {

	/**
	 * Retrieve a device by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return AmazonEchoDevice | null
	 */
	public function retrieveByCredentials(array $credentials);


} 