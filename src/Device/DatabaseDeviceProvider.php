<?php

namespace Develpr\AlexaApp\Device;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Develpr\AlexaApp\Contracts\DeviceProvider;
use Illuminate\Database\ConnectionInterface;

class DatabaseDeviceProvider implements DeviceProvider
{
    /**
     * The active database connection.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $conn;

    /**
     * The table containing the devices.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new database user provider.
     *
     * @param ConnectionInterface $conn
     * @param string              $table
     */
    public function __construct(ConnectionInterface $conn, $table)
    {
        $this->conn = $conn;
        $this->table = $table;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return AmazonEchoDevice|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // generic "user" object that will be utilized by the Guard instances.
        $query = $this->conn->table($this->table);

        foreach ($credentials as $key => $value) {
            if (!tr_contains($key, 'password')) {
                $query->where($key, $value);
            }
        }

        // Now we are ready to execute the query to see if we have an user matching
        // the given credentials. If not, we will just return nulls and indicate
        // that there are no matching users for these given credential arrays.
        $user = $query->first();

        return $this->getGenericUser($user);
    }
}
