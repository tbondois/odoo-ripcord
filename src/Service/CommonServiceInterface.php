<?php

namespace Ripoo\Service;

use Ripoo\OdooClient;
use Ripcord\Client\Client as RipcorClient;

/**
 * @author Thomas Bondois
 * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/common.py
 */
interface CommonServiceInterface
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

    /**
     * @param bool $extended
     * @return string
     */
    public function about($extended = false);

    /**
     * @param $loglevel
     * @param $logger
     * @return bool
     */
    public function set_loglevel($loglevel, $logger = null) : bool;

} // end class
