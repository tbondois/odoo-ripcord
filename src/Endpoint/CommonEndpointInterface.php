<?php

namespace Ripoo\Endpoint;

use Ripoo\Client;
use Ripcord\Client\Client as RipcorClient;

/**
 * @author Thomas Bondois
 */
interface CommonEndpointInterface
{
    /**
     * @return array
     */
    public function version();

    /**
     * @param string $db
     * @param string $username
     * @param string $password
     * @param array $user_agent_env
     * @return string|int
     */
    public function authenticate(string $db, string $username, string $password, array $user_agent_env = []);

} // end class
