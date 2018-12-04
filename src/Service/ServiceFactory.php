<?php

namespace Ripoo\Service;

use Ripoo\OdooClient;
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
     * @return RipcordClient|CommonService|DbService|\Ripoo\Service\ModelService
     * @throws \Ripcord\Exceptions\ConfigurationException
     *
     * @author Thomas Bondois <thomas.bondois@agence-tbd.com>
     */
    public function create(string $endpoint, string $apiUrl, array $options = null, $transport = null)
    {
        if (strpos($apiUrl, $endpoint) === false) {
            $endpointUrl = $apiUrl.'/'.$endpoint;
        } else {
            $endpointUrl = $apiUrl;
        }
        if (!isset($transport)) {
            $transport = new Stream();
        }
        switch ($endpoint) {
            case OdooClient::ENDPOINT_COMMON;
                return new CommonService($endpointUrl, $options, $transport);
                break;
            case OdooClient::ENDPOINT_DB;
                return new DbService($endpointUrl, $options, $transport);
                break;
            case OdooClient::ENDPOINT_MODEL;
                return new ModelService($endpointUrl, $options, $transport);
                break;
            default:
                return new RipcordClient($endpointUrl, $options,$transport);
        }
    }

} // end class
