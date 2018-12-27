<?php

namespace Ripoo;

use Ripcord\Client\Client as RipcordClient;
use Ripcord\Exceptions\ConfigurationException;
use Ripoo\Handler\{CommonHandlerTrait, DbHandlerTrait, ModelHandlerTrait};
use Ripoo\Service\{CommonService, DbService, ModelService, ServiceFactory};
use Ripoo\Exception\{CodingException, ResponseException, ResponseEntryException, ResponseFaultException, ResponseStatusException};

/**
 * Uses Ripcord XML-RPC optimized for Odoo >=8.0
 * @see https://www.odoo.com/documentation/11.0/webservices/odoo.html
 *
 * @author Thomas Bondois
 */
class OdooClient
{
    use CommonHandlerTrait, DbHandlerTrait, ModelHandlerTrait;

    const DEFAULT_API           = 'xmlrpc/2';

    const OPERATION_CREATE      = 'create';
    const OPERATION_WRITE       = 'write';
    const OPERATION_READ        = 'read';
    const OPERATION_UNLINK      = 'unlink';

    const ARRAY_TYPE_NO_ARRAY   = "no";
    const ARRAY_TYPE_EMPTY      = "empty";
    const ARRAY_TYPE_OBJECT     = "object";
    const ARRAY_TYPE_LIST       = "list";
    const ARRAY_TYPE_DICTIONARY = "dict";

    /**
     * Url with protocol and api path to connect to
     * @var string
     */
    private $apiUrl;

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
     * @var null|string
     */
    private $currentEndpoint = null;

    /**
     * For Cache purpose, associative array('endpoint' => Client)
     * @var RipcordClient[]
     */
    private $services = [];

    /**
     * @var ServiceFactory
     */
    private $serviceFactory;

    /**
     * Last response
     * @var mixed scalar or array
     */
    public $response;

    /**
     * @param string $baseUrl The Odoo root url. Must contain the protocol like https://, can also :port or /sub/dir
     * @param null|string $db PostgreSQL database of Odoo containing Odoo tables
     * @param null|string $user The username (Odoo 11 : is email)
     * @param null|string $password Password of the user
     * @param null|string $apiPath if not using xmlrpc/2
     */
    public function __construct(string $baseUrl, $db = null, $user = null, $password = null, $apiPath = null)
    {
        // use customer or default API :
        $apiPath   = self::trimSlash($apiPath ?? self::DEFAULT_API);

        // clean host if it have a final slash :
        $baseUrl    = self::trimSlash($baseUrl);

        $this->apiUrl    = $baseUrl.'/'.$apiPath;
        $this->db        = $db;
        $this->user      = $user;
        $this->password  = $password;
        $this->createdAt = microtime(true);
        $this->pid       = '#'.$apiPath.'-'.microtime(true)."-".mt_rand(10000, 99000);

        $this->serviceFactory = new ServiceFactory();
    }

    /**
     * @return null|string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
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
     * @return null|string
     */
    public function getCurrentEndpoint()
    {
        return $this->currentEndpoint;
    }

    /**
     * @return RipcordClient|CommonService|DbService|ModelService
     * @throws CodingException
     */
    public function getCurrentService() // : RipcordClient //only php7.2 manage child classes without warning
    {
        if (!$this->currentEndpoint || empty($this->services[$this->currentEndpoint])) {
            throw new CodingException("Need to make a first call before getting the current client");
        }
        return $this->services[$this->currentEndpoint];
    }

    /**
     * Get XmlRpc Client, manage cache
     *
     * This method returns an XmlRpc Client for the requested endpoint.
     * If no endpoint is specified or if a client for the requested endpoint is
     * already initialized, the last used client will be returned.
     *
     * @param string $endpoint The api endpoint
     * @return RipcordClient|CommonService|DbService|ModelService
     * @throws ConfigurationException
     */
    public function getService(string $endpoint) // : RipcordClient //only php7.2 manage child classes without warning
    {
        $endpoint = self::trimSlash($endpoint);
        if (empty($this->services[$endpoint])) {
            $this->services[$endpoint] = $this->serviceFactory->create($endpoint, $this->apiUrl);
        }
        $this->currentEndpoint = $endpoint;
        return $this->services[$endpoint];
    }

    /**
     * Throw exceptions in case the reponse contains error declarations
     * @TODO check keys "status", "status_message" and raised exception "Error"
     *
     * @param mixed $response
     * @return bool
     * @throws ResponseFaultException|ResponseStatusException
     * @author Thomas Bondois
     */
    public function checkResponse($response)
    {
        if (is_array($response)) {
            if (isset($response['faultCode'])) {
                $faultCode = $response['faultCode'];
                $faultString = $response['faultString'] ?? '';
                throw new ResponseFaultException($faultString, $faultCode);
            }
            if (isset($response['status'])) {
                $status = $response['status'];
                $statusMessage = $response['status_message'] ?? $response['statusMessage'] ?? '';
                throw new ResponseStatusException($statusMessage, $status);
            }
        }
        return true;
    }

    /**
     * @param mixed $response scalar or array
     * @return mixed|null response
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function setResponse($response)
    {
        $this->response = null;

        if ($this->checkResponse($response)) {
            $this->response = $response;
        }
        return $this->response;
    }

    /**
     * get last response
     * @return mixed scalar or array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isResponseSuccess() : bool
    {
        try {
            $response = $this->checkResponse($this->response);
            if ('' !== $response && null !== $response) {
                return true;
            }
        } catch (ResponseException $e) {
        }
        return false;
    }

    /**
     * @param string|int ...$keys
     * @return mixed scalar or array
     * @throws ResponseEntryException
     */
    public function getResponseEntry(...$keys)
    {
        $entryValue = $this->getResponse();
        if (is_array($entryValue) && count($keys)) {
            foreach ($keys as $key) {
                if (isset($entryValue[$key])) {
                    $entryValue = $entryValue[$key];
                } else {
                    throw new ResponseEntryException(sprintf("entry '%s' not found in (%s) response", $key, gettype($this->response)));
                }
            }
            return $entryValue;
        } else {
            throw new ResponseEntryException(sprintf("invalid response format (%s) or no input keys (%s)", gettype($this->response), count($keys)));
        }
    }

    /**
     * Useful to avoid bad URL-related input.
     * @param $str
     * @param null $extraChars
     * @return string
     * @author Thomas Bondois
     */
    public static function trimSlash($str, $extraChars = null)
    {
        $charlist = " \t\n\r\0\x0B"; //default trim charlist
        $charlist.= "/";
        if (null !== $extraChars) {
            $charlist.= $extraChars;
        }
        return trim($str, $charlist);
    }


    /**
     * Determine if a PHP array can be considered as sequential (numeric-ordered keys) like Python List/Tuple ou associative (like Python Dictionary), etc
     * @param array $var
     * @return string
     * @author Thomas Bondois
     */
    public function getArrayType($var) : string
    {
        if (!is_array($var)) {
            if (is_object($var) && $var instanceof \Traversable && $var instanceof \Countable) {
                return self::ARRAY_TYPE_OBJECT; // ~Collection object
            }
            return self::ARRAY_TYPE_NO_ARRAY; // Not an array
        }
        $count = count($var);
        if (!$count) {
            return self::ARRAY_TYPE_EMPTY; // Empty, so we can't define the python type
        }
        if (array_keys($var) === range(0, $count-1)) {
            return self::ARRAY_TYPE_LIST; // python List or Tuple, or even few chances of being a Dictionary
        }
        return self::ARRAY_TYPE_DICTIONARY; // python Dictionary
    }

} // end class
