<?php

namespace Ripoo;

use Ripoo\Exception\ResponseFaultException;
use Ripoo\Exception\CodingException;
use Ripoo\Exception\ResponseStatusException;
use Ripoo\Handler\CommonHandler;
use Ripoo\Handler\DbHandler;
use Ripoo\Handler\ModelHandler;
use Ripoo\Service\ServiceFactory;
use Ripcord\Client\Client as RipcordClient;

/**
 * Uses Ripcord XML-RPC optimized for Odoo >=8.0
 * @see https://www.odoo.com/documentation/11.0/webservices/odoo.html
 *
 * @author Thomas Bondois
 */
class OdooClient
{
    use CommonHandler, DbHandler, ModelHandler;

    const DEFAULT_API       = 'xmlrpc/2';

    const ENDPOINT_MODEL    = 'object';
    const ENDPOINT_COMMON   = 'common';
    const ENDPOINT_DB       = 'db';

    const OPERATION_CREATE  = 'create';
    const OPERATION_WRITE   = 'write';
    const OPERATION_READ    = 'read';
    const OPERATION_UNLINK  = 'unlink';

    /**
     * Host to connect to
     * @var string
     */
    private $url;

    /**
     * Unique identifier for current user
     * @var integer
     */
    private $uid;

    /**
     * Current users username
     * @var string
     */
    private $user;

    /**
     * Current database
     * @var string
     */
    private $db;

    /**
     * Password for current user
     * @var string
     */
    private $password;

    /**
     * micro timestamp
     * @var float
     */
    private $createdAt;

    /**
     * unique client instance identifier
     * @var string
     */
    private $pid;

    /**
     * @var string
     */
    private $currentService = null;

    /**
     * For Cache purpose, associative array('endpoint' => Client)
     * @var RipcordClient[]
     */
    private $services = [];

    protected $serviceFactory;

    /**
     * @param string $baseUrl The Odoo root url. Must contain the protocol like https://, can also :port or /sub/dir
     * @param ?string $db PostgreSQL database of Odoo containing Odoo tables
     * @param ?string $user The username (Odoo 11 : is email)
     * @param ?string $password Password of the user
     * @param ?string $apiPath if not using xmlrpc/2
     */
    public function __construct(string $baseUrl, $db = null, $user = null, $password = null, $apiPath = null)
    {
        $this->serviceFactory = new ServiceFactory();

        // use customer or default API :
        $apiPath = trim($apiPath ?? self::DEFAULT_API, ' /');

        // clean host if it have a final slash :
        $baseUrl    = trim($baseUrl, ' /');

        $this->url       = $baseUrl . '/' . $apiPath;
        $this->db        = $db;
        $this->user      = $user;
        $this->password  = $password;
        $this->createdAt = microtime(true);
        $this->pid       = '#'.$apiPath.'-'.microtime(true)."-".mt_rand(10000, 99000);
    }

    /**
     * @param bool $raw 0 = formatted date, 1 = float (micro timestamp)
     * @return mixed
     */
    public function getCreatedAt($raw = false)
    {
        if (!$raw) {
            return date('Y-m-d H:i:s', $this->createdAt);
        }
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Get XmlRpc Client
     *
     * This method returns an XmlRpc Client for the requested endpoint.
     * If no endpoint is specified or if a client for the requested endpoint is
     * already initialized, the last used client will be returned.
     *
     * @param string $endpoint The api endpoint
     * @return RipcordClient
     * @throws \Ripcord\Exceptions\ConfigurationException
     */
    public function getRipcordClient(string $endpoint) : RipcordClient
    {
        $endpoint = trim($endpoint, " /");
        if (!empty($this->services[$endpoint])) {
            return $this->services[$endpoint];
        }
        //$this->services[$endpoint] = Ripcord::client($this->url.'/'.$endpoint);
        $this->services[$endpoint] = $this->serviceFactory->create($endpoint, $this->url);
        $this->currentService = $endpoint;
        return $this->services[$endpoint];
    }

    public function getCurrentRipcordClient() : RipcordClient
    {
        if (!$this->currentService || empty($this->services[$this->currentService])) {
            throw new CodingException("Need to make a first call before getting the current client");
        }
        return $this->services[$this->currentService];
    }

    /**
     * Throw exceptions in case the reponse contains error declarations
     * @TODO check keys "status", "status_message" and raised exception "Error"
     *
     * @param mixed $response
     * @return mixed
     * @throws ResponseFaultException
     * @author Thomas Bondois
     */
    public function formatResponse($response)
    {
        if (is_array($response)) {
            if (isset($response['faultCode'])) {
                $faultCode = $response['faultCode'];
                $faultString = $response['faultString'] ?? '';
                throw new ResponseFaultException($faultString, $faultCode);
            }
        }
        return $response;
    }

} // end class
