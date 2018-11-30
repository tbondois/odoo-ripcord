<?php

namespace Ripoo;

use Ripoo\Exception\RipooExceptionInterface;
use Ripoo\Exception\AuthException;
use Ripoo\Exception\ConnectException;
use Ripoo\Exception\OdooException;

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
     * microtimestamp
     * @var float
     */
    private $createdAt;

    /**
     * micro timestamp + rand
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
     * @param bool $forceRetry
     * @return bool
     * @throws AuthException
     */
    public function authenticate(bool $forceRetry = false) : bool
    {
        if ($forceRetry) {
            $this->uid = null;
        }
        if ($this->uid()) {
            return true;
        }
        return false;
    }

    /**
     * @param string $model
     * @param string $permission ex: 'read'
     * @param bool $withExceptions
     * @return bool
     */
    public function check_access_rights(string $model, string $permission = 'read', bool $withExceptions = false) : bool
    {
        if (!is_array($permission)) {
            $permission = [$permission];
        }
        try {
            $response = $this->getRipcordClient('object')->execute_kw(
                $this->db, $this->uid(), $this->password,
                $model,
                'check_access_rights',
                $permission,
                ['raise_exception' => $withExceptions]
            );

            //TODO analyse result fault etc
            return (bool)$response;

        } catch (RipooExceptionInterface $exception) {
        }
        return false;
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
     * @throws AuthException
     */
    public function search(string $model, array $criteria, $offset = 0, $limit = 100, $order = '')
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
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
     * @throws AuthException
     */
    public function search_count(string $model, array $criteria)
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
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
     * @throws AuthException
     */
    public function read(string $model, array $ids, array $fields = [])
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
     * @param string $order
     *
     * @return array An array of models
     * @throws AuthException
     */
    public function search_read(string $model, array $criteria, array $fields = [], int $limit = 100, $order = '')
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search_read',
            [$criteria],
            [
                'fields' => $fields,
                'limit' => $limit,
                'order' => $order,
            ]
        );
        return $response;
    }


    /**
     * @see https://www.odoo.com/documentation/11.0/reference/orm.html#odoo.models.Model.fields_get
     * @param string $model
     * @param array $fields
     * @param array $attributes
     * @return mixed
     * @throws AuthException
     */
    public function fields_get(string $model, array $fields = [], array $attributes = [])
    {
        $response = $this->getRipcordClient('object')->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'fields_get',
            $fields,
            ['attributes' => $attributes]
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
     * @throws AuthException
     */
    public function create(string $model, $data)
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
     * @throws AuthException
     */
    public function write(string $model, $ids, $fields)
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
     * @throws AuthException
     */
    private function unlink(string $model, $ids)
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
     * Get uid
     *
     * @return int $uid
     * @throws AuthException
     */
    private function uid()
    {
        if ($this->uid === null) {
            $client = $this->getRipcordClient('common');
            $this->uid = $client->authenticate(
                $this->db, $this->user, $this->password,
                []
            );

            if (!is_int($this->uid)) {
                if (is_array($this->uid) && array_key_exists('faultCode', $this->uid)) {
                    throw new AuthException($this->uid['faultCode']);
                } else {
                    throw new AuthException('Unsuccessful Authorization');
                }
            }
        }
        return $this->uid;
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
     * @param bool $raw 0 = formatted date, 1 = float (micro timestamp)
     * @return mixed
     */
    public function getCreatedAt($raw = false)
    {
        if ($raw) {
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
