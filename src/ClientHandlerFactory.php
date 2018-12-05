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
class ClientHandlerFactory
{
    /**
     * @param string $baseUrl
     * @param ?string $db
     * @param ?string $user
     * @param ?string $password
     * @param ?string $apiPath
     *
     * @return ClientHandler
     */
    public function create(string $baseUrl, $db = null, $user = null, $password = null, $apiPath  = null) : ClientHandler
    {
        return new ClientHandler($baseUrl, $db, $user, $password, $apiPath);
    }

    /**
     * More strict instance creator
     *
     * @param string $baseUrl
     * @param string $db
     * @param string $user
     * @param string $password
     * @param ?string $apiType
     *
     * @return ClientHandler
     */
    public function createAuthenticated(string $baseUrl, string $db , string $user, string $password, $apiPath  = null) : ClientHandler
    {
        return new ClientHandler($baseUrl, $db, $user, $password, $apiPath);
    }

    /**
     * Useful only for API method not requiring authentification, like version() and server_version()
     *
     * @param string $baseUrl
     * @param ?string $apiPath
     * @return ClientHandler
     */
    public function createAnonymous($baseUrl, $apiPath = null) : ClientHandler
    {
        return new ClientHandler($baseUrl, null, null, null, $apiPath);
    }

} // end class
