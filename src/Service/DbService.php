<?php

namespace Ripoo\Service;

use Ripcord\Client\Client as RipcordClient;

/**
 * Reflect of Odoo Db Service
 * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/db.py
 *
 * @method server_version() : string
 * @method db_exist($db_name) : bool
 * @method list($document = false) : array
 * @method list_lang() : array
 * @method list_countries() : array
 *
 * @author Thomas Bondois
 */
class DbService extends RipcordClient
{
    public function __construct($url, $options = null, $transport = null)
    {
        parent::__construct($url, $options, $transport);
    }

} // end class
