<?php

namespace Ripoo\Handler;

use Ripoo\Service\DbService;

/**
 * Handle methods related to Odoo Db Service/Endpoint
 * @author Thomas Bondois
 */
trait DbHandlerTrait
{
    /**
     * odoo.service.db.dispatch
     * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/db.py
     *
     * @return DbService
     */
    public function getDbService() : DbService
    {
        return $this->getService(DbService::ENDPOINT);
    }

    /**
     * @return string
     */
    public function server_version()
    {
        $response = $this->getDbService()->server_version();
        return $this->setResponse($response);
    }

    /**
     * If PostgreSQL database exists
     * @param $db_name
     * @return bool
     * @throws ResponseException
     */
    public function db_exist($db_name)
    {
        $response = $this->getDbService()->db_exist($db_name);
        return (bool)$this->setResponse($response);
    }


    /**
     * Get PostgreSQL databases from config
     * @param bool $document
     * @return array
     * @throws ResponseException
     */
    public function list_dbs($document = false): array
    {
        $response = $this->getDbService()->list($document);
        return $this->setResponse($response);
    }

    /**
     * @return array
     * @throws ResponseException
     */
    public function list_lang(): array
    {
        $response = $this->getDbService()->list_lang();
        return $this->setResponse($response);
    }

    /**
     * @return array
     * @throws ResponseException
     */
    public function list_countries(): array
    {
        $response = $this->getDbService()->list_countries();
        return $this->setResponse($response);
    }

}
