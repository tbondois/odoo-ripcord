<?php

namespace OdooRipcord;

use Ripcord\Ripcord;
use Ripcord\Client\Client as RipcordClient;

/**
 * Uses ripcord for Odoo 11.0
 * @see https://www.odoo.com/documentation/11.0/webservices/odoo.html
 * @see https://github.com/DarkaOnLine/Ripcord
 * @see https://github.com/robroypt/odoo-client
 *
 * @author Thomas Bondois
 */
class Client
{
    const DEFAULT_API_TYPE = 'xmlrpc/2';

    /**
     * Ripcord Client
     * @var RipcordClient
     */
    private $client;

    /**
     * Host to connect to
     * @var string
     */
    private $host;

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
     * XmlRpc endpoint
     * @var string
     */
    private $endpoint;

    /**
     * @var float
     */
    private $createdAt;

    /**
     * @var string
     */
    private $pid;

    /**
     * @param string $host The url. Can contain the post or extra path
     * @param string $db The postgresql database to log into
     * @param string $user The username
     * @param string $password Password of the user
     * @param null|string $apiType Password of the user
     */
    public function __construct($host, $db, $user, $password, $apiType = null)
    {
        if ($apiType === null) {
            $apiType = self::DEFAULT_API_TYPE;
        }
        $this->host      = trim($host, '/') . '/' . trim($apiType, '/');
        $this->db        = $db;
        $this->user      = $user;
        $this->password  = $password;
        $this->createdAt = microtime(true);
        $this->pid       = microtime(true)."/".mt_rand(10000, 99000);
    }

    /**
     * Get version
     *
     * @return array Odoo version
     */
    public function version()
    {
        $response = $this->getRipcordClient('common')->version();
        return $response;
    }

    /**
     * Search models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param integer $offset Offset
     * @param integer $limit Max results
     *
     * @return array Array of model id's
     */
    public function search($model, $criteria, $offset = 0, $limit = 100, $order = '')
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db,
            $this->uid(),
            $this->password,
            $model,
            'search',
            [$criteria],
            ['offset' => $offset, 'limit' => $limit, 'order' => $order]
        );
        return $response;
    }

    /**
     * Search_count models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     *
     * @return array Array of model id's
     */
    public function search_count($model, $criteria)
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db,
            $this->uid(),
            $this->password,
            $model,
            'search_count',
            [$criteria]
        );
        return $response;
    }

    /**
     * Read model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model id's
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     *
     * @return array An array of models
     */
    public function read($model, $ids, $fields = [])
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'read',
            [$ids],
            ['fields' => $fields]
        );
        return $response;
    }

    /**
     * Search and Read model(s)
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     * @param integer $limit Max results
     *
     * @return array An array of models
     */
    public function search_read($model, $criteria, $fields = [], $limit = 100, $order = '')
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search_read',
            [$criteria],
            ['fields' => $fields, 'limit' => $limit, 'order' => $order]
        );
        return $response;
    }

    /**
     * Create model
     *
     * @param string $model Model
     * @param array $data Array of fields with data (format: ['field' => 'value'])
     *
     * @return int Created model id
     */
    public function create($model, $data)
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'create',
            [$data]
        );
        return $response;
    }

    /**
     * Update model(s)
     *
     * @param string $model Model
     * @param array $ids Model ids to update
     * @param array $fields A associative array (format: ['field' => 'value'])
     *
     * @return array
     */
    public function write($model, $ids, $fields)
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'write',
            [
                $ids,
                $fields,
            ]
        );
        return $response;
    }

    /**
     * Unlink model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model id's
     *
     * @return boolean True is successful
     */
    public function unlink($model, $ids)
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'unlink',
            [$ids]
        );
        return $response;
    }

    /**
     * Get XmlRpc Client
     *
     * This method returns an XmlRpc Client for the requested endpoint.
     * If no endpoint is specified or if a client for the requested endpoint is
     * already initialized, the last used client will be returned.
     *
     * @param null|string $endpoint The api endpoint
     *
     * @return RipcordClient
     */
    private function getRipcordClient($endpoint = null) : RipcordClient
    {
        if ($endpoint === null) {
            return $this->client;
        }
        if ($this->endpoint === $endpoint) {
            return $this->client;
        }
        $this->endpoint = $endpoint;
        $this->client = Ripcord::client($this->host.'/'.$endpoint);
        return $this->client;
    }
    /**
     * Get uid
     *
     * @return int $uid
     */
    private function uid()
    {
        if ($this->uid === null) {
            $client = $this->getRipcordClient('common');
            $this->uid = $client->authenticate(
                $this->db,
                $this->user,
                $this->password,
                []
            );
        }
        return $this->uid;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt($asString = false)
    {
        if ($asString) {
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





} // end class
