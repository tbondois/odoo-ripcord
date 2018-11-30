<?php

namespace Ripoo\Endpoint;

use Ripoo\Client;
use Ripcord\Client\Client as RipcorClient;

/**
 * @author Thomas Bondois
 */
interface ObjectEndpointInterface
{
    /**
     * @param string $db
     * @param string $uid
     * @param string $password
     * @param string $model
     * @param string $method
     * @param array $args
     * @param null|array $kw_args
     * @return array|int|bool
     */
    public function execute_kw(string $db, string $uid, string $password, string $model, string $method, array $args = [], array $kw_args = []);

} // end class
