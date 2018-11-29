<?php

namespace OdooRipcord;

/**
 * Uses ripcord for Odoo 11.0
 * @see https://www.odoo.com/documentation/11.0/webservices/odoo.html
 * @see https://github.com/DarkaOnLine/Ripcord
 * @see https://github.com/robroypt/odoo-client
 *
 * @author Thomas Bondois
 */
class ClientFactory
{
    /**
     * @param string $host The url. Can contain the post or extra path
     * @param string $db The postgresql database to log into
     * @param string $user The username
     * @param string $password Password of the user
     * @param null|string $apiType Password of the user
     *
     * @return Client
     */
    public function create($host, $db, $user, $password, $apiType  = null) : Client
    {
        return new Client($host, $db, $user, $password, $apiType);
    }


} // end class
