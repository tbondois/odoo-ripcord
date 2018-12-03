<?php

namespace Ripoo;

/**
 * Factory for creating Client
 * @see https://www.odoo.com/documentation/11.0/webservices/odoo.html
 * @see https://github.com/DarkaOnLine/Ripcord
 * @see https://github.com/robroypt/odoo-client
 *
 * @author Thomas Bondois
 */
class OdooClientFactory
{
    /**
     * @param string $url
     * @param ?string $db
     * @param ?string $user
     * @param ?string $password
     * @param ?string $apiPath
     * @return OdooClient
     */
    public function create(string $url, $db = null, $user = null, $password = null, $apiPath  = null) : OdooClient
    {
        return new OdooClient($url, $db, $user, $password, $apiPath);
    }

    /**
     * More strict instance creator
     * @param string $url
     * @param string $db
     * @param string $user
     * @param string $password
     * @param ?string $apiType
     * @return OdooClient
     */
    public function createAuthenticated(string $url, string $db , string $user, string $password, $apiPath  = null) : OdooClient
    {
        return new OdooClient($url, $db, $user, $password, $apiPath);
    }


    /**
     * Useful only for API method not requiring authentification, like version() and server_version()
     * @param string $url
     * @param ?string $apiPath
     * @return OdooClient
     */
    public function createAnonymous($url, $apiPath = null) : OdooClient
    {
        return new OdooClient($url, null, null, null, $apiPath);
    }

} // end class
