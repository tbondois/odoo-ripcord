<?php

namespace Ripoo\Service;

use Ripcord\Client\Client as RipcordClient;

/**
 * Reflect of Odoo Common Service
 * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/model.py
 *
 * @method authenticate(string $db, string $username, string $password, array $user_agent_env = []) : int
 * @method version() : array
 *
 * @author Thomas Bondois
 */
class CommonService extends RipcordClient
{
    public function __construct($url, $options = null, $transport = null)
    {
        parent::__construct($url, $options, $transport);
    }

} // end class
