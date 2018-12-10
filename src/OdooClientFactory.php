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
    public function create(string $baseUrl, $db = null, $user = null, $password = null, $apiPath  = null) : OdooClient
    {
        return new OdooClient($baseUrl, $db, $user, $password, $apiPath);
    }

    /**
     * More strict instance parameters
     *
     * @param string $baseUrl
     * @param string $db
     * @param string $user
     * @param string $password
     * @param null|string $apiPath
     *
     * @return OdooClient
     */
    public function createAuthenticated(string $baseUrl, string $db , string $user, string $password, $apiPath  = null) : OdooClient
    {
        return $this->create($baseUrl, $db, $user, $password, $apiPath);
    }

    /**
     * Useful only for API method not needing authentification, like 'common' and 'db' endpoints
     *
     * @param string $baseUrl
     * @param null|string $apiPath
     * @return OdooClient
     */
    public function createAnonymous($baseUrl, $apiPath = null) : OdooClient
    {
        return $this->create($baseUrl, null, null, null, $apiPath);
    }

} // end class
