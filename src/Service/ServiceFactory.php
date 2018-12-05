<?php

namespace Ripoo\Service;

use Ripoo\ClientHandler;
use Ripcord\Client\Client as RipcordClient;
use Ripcord\Client\Transport\Stream;


/**
 * Factory for creating services
 * @see https://www.odoo.com/documentation/11.0/webservices/odoo.html
 * @see https://github.com/DarkaOnLine/Ripcord
 * @see https://github.com/robroypt/odoo-client
 *
 * @author Thomas Bondois
 */
class ServiceFactory
{
    /**
     * @param string $endpoint
     * @param string $apiUrl
     * @param array|null $options
     * @param null $transport
     *
     * @return RipcordClient|CommonService|DbService|ModelService
     * @throws \Ripcord\Exceptions\ConfigurationException
     *
     * @author Thomas Bondois
     */
    public function create(string $endpoint, string $apiUrl, array $options = null, $transport = null) : RipcordClient
    {
        $endpointUrl = ClientHandler::trimSlash($apiUrl).'/'.ClientHandler::trimSlash($endpoint);

        if (!$transport) {
            $transport = new Stream();
        }
        switch ($endpoint) {
            case ClientHandler::ENDPOINT_COMMON;
                return new CommonService($endpointUrl, $options, $transport);
            case ClientHandler::ENDPOINT_DB;
                return new DbService($endpointUrl, $options, $transport);
            case ClientHandler::ENDPOINT_MODEL;
                return new ModelService($endpointUrl, $options, $transport);
            default:
                return new RipcordClient($endpointUrl, $options,$transport);
        }
    }

} // end class
