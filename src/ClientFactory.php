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
class ClientFactory
{
    /**
     * @param string $url The Odoo url. Must contain the protocol like https://, can also :port or /sub/directories
     * @param string $db The PostgreSQL database of Odoo to log into
     * @param string $user The username (Odoo 11 : is email)
     * @param string $password Password of the user
     * @param ?string $apiType if not using xmlrpc/2
     *
     * @return Client
     */
    public function create(string $url, string $db , string $user, string $password, $apiType  = null) : Client
    {
        return new Client($url, $db, $user, $password, $apiType);
    }

    /**
     * Useful only for API method not requiring authentification, like version() and server_version()
     * @param $url
     *
     * @return Client
     */
    public function createAnonymous($url) : Client
    {
        return new Client($url);
    }

} // end class
