<?php

namespace Develpr\AlexaApp\Device;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Develpr\AlexaApp\Contracts\DeviceProvider;

class EloquentDeviceProvider implements DeviceProvider
{
    /**
     * The model to be used
     *
     * @var string
     */
    private $model;

    /**
     * EloquentDeviceProvider constructor.
     *
     * @param string $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Retrieve a device by the given credentials.
     *
     * @param array $credentials
     *
     * @return AmazonEchoDevice|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->createModel()->newQuery();

        foreach ($credentials as $key => $value) {
            if (!str_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Create a new instance of the model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class();
    }
}
