<?php

namespace Ripoo\Handler;

use Ripoo\Service\DbService;

use Ripoo\Exception\ResponseFaultException;
use Ripoo\Exception\ResponseStatusException;

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
        return $this->getService(self::ENDPOINT_DB);
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
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function db_exist($db_name)
    {
        $response = $this->getDbService()->db_exist($db_name);
        return (bool)$this->formatResponse($response);
    }


    /**
     * @param bool $document
     * @return array
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function list($document = false): array
    {
        $response = $this->getDbService()->list($document);
        return $this->formatResponse($response);
    }

    /**
     * @return array
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function list_lang(): array
    {
        $response = $this->getDbService()->list_lang();
        return $this->formatResponse($response);
    }

    /**
     * @return array
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function list_countries(): array
    {
        $response = $this->getDbService()->list_countries();
        return $this->formatResponse($response);
    }

}
