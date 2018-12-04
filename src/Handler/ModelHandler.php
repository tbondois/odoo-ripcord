<?php

namespace Ripoo\Handler;

use Ripoo\Service\ModelService;
use Ripoo\Exception\RipooExceptionInterface;
use Ripoo\Exception\AuthException;
use Ripoo\Exception\ResponseFaultException;

/**
 * Handle related to Odoo Model/Object Service/Endpoint
 * @see https://www.odoo.com/documentation/11.0/reference/orm.html#reference-orm-model
 * @author Thomas Bondois
 */
trait ModelHandler
{
    /**
     * "Object" Endpoint, "Model" service
     * odoo.service.model.dispatch
     *
     * @return ModelService
     *
     * @author Thomas Bondois
     */
    public function getModelService()
    {
        return $this->getRipcordClient(self::ENDPOINT_MODEL);
    }

    /**
     * @see https://odoo-restapi.readthedocs.io/en/latest/calling_methods/check_access_rights.html
     *
     * @param string $model
     * @param string $permission see OPERATION_* constants
     * @param bool $withExceptions
     * @return bool
     *
     * @author Thomas Bondois
     */
    public function check_access_rights(string $model, string $permission = self::OPERATION_READ, bool $withExceptions = false)
    {
        if (!is_array($permission)) {
            $permission = [$permission];
        }
        try {
            $response = $this->getModelService()->execute_kw(
                $this->db, $this->uid(), $this->password,
                $model,
                'check_access_rights',
                $permission,
                ['raise_exception' => $withExceptions]
            );

            //TODO analyse result fault etc
            return (bool)$this->formatResponse($response);

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
     * @param string $order
     * @return array Array of model id's
     * @throws AuthException
     * @throws ResponseFaultException
     */
    public function search(string $model, array $criteria, $offset = 0, $limit = 100, $order = '')
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search',
            [$criteria],
            ['offset' => $offset, 'limit' => $limit, 'order' => $order]
        );
        return $this->formatResponse($response);
    }

    /**
     * Search_count models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     *
     * @return int
     * @throws AuthException
     * @throws ResponseFaultException
     */
    public function search_count(string $model, array $criteria)
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search_count',
            [$criteria]
        );
        return $this->formatResponse($response);
    }

    /**
     * Read model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model id's
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     *
     * @return array An array of models
     * @throws ResponseFaultException
     */
    public function read(string $model, array $ids, array $fields = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'read',
            [$ids],
            ['fields' => $fields]
        );
        return $this->formatResponse($response);
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
     * @throws ResponseFaultException
     */
    public function search_read(string $model, array $criteria, array $fields = [], int $limit = 100, $order = '')
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search_read',
            [$criteria],
            [
                'fields' => $fields,
                'limit'  => $limit,
                'order'  => $order,
            ]
        );
        return $this->formatResponse($response);
    }

    /**
     * @see https://www.odoo.com/documentation/11.0/reference/orm.html#odoo.models.Model.fields_get
     *
     * @param string $model
     * @param array $fields
     * @param array $attributes
     *
     * @return mixed
     * @throws AuthException
     * @throws ResponseFaultException
     *
     * @author Thomas Bondois
     */
    public function fields_get(string $model, array $fields = [], array $attributes = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'fields_get',
            $fields,
            ['attributes' => $attributes]
        );
        return $this->formatResponse($response);
    }

    /**
     * Create model
     *
     * @param string $model Model
     * @param array $data Array of fields with data (format: ['field' => 'value'])
     *
     * @return int Created model id
     * @throws AuthException
     * @throws ResponseFaultException
     */
    public function create(string $model, $data)
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'create',
            [$data]
        );
        return $this->formatResponse($response);
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
     * @throws ResponseFaultException
     */
    public function write(string $model, $ids, $fields)
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'write',
            [
                $ids,
                $fields,
            ]
        );
        return $this->formatResponse($response);
    }

    /**
     * Unlink model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model id's
     *
     * @return boolean True is successful
     * @throws AuthException
     * @throws ResponseFaultException
     */
    public function unlink(string $model, $ids)
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'unlink',
            [$ids]
        );
        return $this->formatResponse($response);
    }

} // end class
