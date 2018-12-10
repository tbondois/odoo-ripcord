<?php

namespace Ripoo\Service;

use Ripoo\OdooClient;
use Ripcord\Client\Client as RipcordClient;
use Ripcord\Client\Transport\Stream;
use Ripcord\Exceptions\ConfigurationException;

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
     * @throws ConfigurationException
     */
    public function create(string $endpoint, string $apiUrl, array $options = null, $transport = null) // : RipcordClient //only php7.2 manage child classes without warning
    {
        $endpointUrl = OdooClient::trimSlash($apiUrl).'/'.OdooClient::trimSlash($endpoint);

        if (!$transport) {
            $transport = new Stream();
        }
        switch ($endpoint) {
            case CommonService::ENDPOINT;
                return new CommonService($endpointUrl, $options, $transport);
            case DbService::ENDPOINT;
                return new DbService($endpointUrl, $options, $transport);
            case ModelService::ENDPOINT;
                return new ModelService($endpointUrl, $options, $transport);
            default:
                return new RipcordClient($endpointUrl, $options,$transport);
        }
    }

} // end class
