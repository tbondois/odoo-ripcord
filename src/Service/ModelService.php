<?php

namespace Ripoo\Service;

use Ripcord\Client\Client as RipcordClient;

/**
 * Reflect of Odoo Model Service
 * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/model.py
 *
 * @method execute_kw(string $db, string $uid, string $password, string $model, string $method, array $args = [], array $kw = null)
 * @method execute(string $db, string $uid, string $password, string $model, string $method, array $args = [], array $kw = [])
 *
 * @author Thomas Bondois
 */
class ModelService extends RipcordClient
{
    public function __construct($url, $options = null, $transport = null)
    {
        parent::__construct($url, $options, $transport);
    }

} // end class
