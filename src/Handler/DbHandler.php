<?php

namespace Ripoo\Handler;

use Ripoo\Service\DbService;

/**
 * Handle methods related to Odoo Db Service/Endpoint
 * @author Thomas Bondois
 */
trait DbHandler
{
    /**
     * odoo.service.db.dispatch
     * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/db.py
     *
     * @return DbService
     */
    public function getDbService() : DbService
    {
        return $this->getRipcordClient(self::ENDPOINT_DB);
    }

    /**
     * @return string
     */
    public function server_version()
    {
        $response = $this->getDbService()->server_version();
        return $this->formatResponse($response);
    }

    /**
     * @param $db_name
     * @return bool
     */
    public function db_exist($db_name): bool
    {
        $response = $this->getDbService()->db_exist($db_name);
        return (bool)$this->formatResponse($response);
    }

}
