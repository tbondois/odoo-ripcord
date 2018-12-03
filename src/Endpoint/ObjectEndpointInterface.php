<?php

namespace Ripoo\Endpoint;

use Ripoo\Client;
use Ripcord\Client\Client as RipcorClient;

/**
 * @author Thomas Bondois
 * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/model.py
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
     * @param ?array $kw
     * @return array|int|bool
     */
    public function execute_kw(string $db, string $uid, string $password, string $model, string $method, array $args = [], $kw = null);

    /**
     * @param string $db
     * @param string $uid
     * @param string $password
     * @param string $model
     * @param string $method
     * @param array $args
     * @param array $kw
     * @return array|int|bool
     */
    public function execute(string $db, string $uid, string $password, string $model, string $method, array $args = [], $kw = []);


} // end class
