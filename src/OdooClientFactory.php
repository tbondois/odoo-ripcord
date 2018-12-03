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
     * @param string $url The Odoo url. Must contain the protocol like https://, can also :port or /sub/directories
     * @param ?string $db The PostgreSQL database of Odoo to log into
     * @param ?string $user The username (Odoo 11 : is email)
     * @param ?string $password Password of the user
     * @param ?string $apiType if not using xmlrpc/2
     * @return OdooClient
     */
    public function create(string $url, $db = null, $user = null, $password = null, $apiType  = null) : OdooClient
    {
        return new OdooClient($url, $db, $user, $password, $apiType);
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
    public function createAuthenticated(string $url, string $db , string $user, string $password, $apiType  = null) : OdooClient
    {
        return new OdooClient($url, $db, $user, $password, $apiType);
    }


    /**
     * Useful only for API method not requiring authentification, like version() and server_version()
     * @param string $url
     * @param ?string $apiTyppe
     * @return OdooClient
     */
    public function createAnonymous($url, $apiType = null) : OdooClient
    {
        return new OdooClient($url, null, null, null, $apiType);
    }

} // end class
