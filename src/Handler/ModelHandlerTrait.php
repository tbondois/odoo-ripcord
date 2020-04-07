<?php

namespace Ripoo\Handler;

use Ripoo\Service\ModelService;
use Ripoo\Exception\{RipooException, AuthException, ResponseException, ResponseFaultException, ResponseStatusException};

/**
 * Handle related to Odoo Model/Object Service/Endpoint
 * @see https://www.odoo.com/documentation/11.0/reference/orm.html#reference-orm-model
 * @author Thomas Bondois
 */
trait ModelHandlerTrait
{
    /**
     * "Object" Endpoint, "Model" service
     * odoo.service.model.dispatch
     *
     * @return ModelService
     */
    public function getModelService() : ModelService
    {
        return $this->getService(ModelService::ENDPOINT);
    }

    /**
     * @param string $model
     * @param string $method
     * @param array|null $args argument list, ordered. sequential-array (Python-List) containing, for each numeric index, scalar or array
     * @param array|null $kwargs extra argument list, named. associative-array  (Python-Dictionary) containing, for each keyword, scalar or array
     * @return mixed
     *
     * @author Thomas Bondois
     */
    public function model_execute_kw(string $model, string $method, $args = null, $kwargs = null)
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            $method,
            $args,
            $kwargs
        );
        return $this->setResponse($response);
    }

    /**
     * @param string $model
     * @param string $method
     * @param mixed ...$args
     * @return mixed
     * @author Thomas Bondois <thomas.bondois@agence-tbd.com>
     */
    public function model_execute_splat(string $model, string $method, ...$args)
    {
        $response = $this->getModelService()->execute(
            $this->db, $this->uid(), $this->password,
            $model,
            $method,
            $args
        );
        return $this->setResponse($response);
    }

    /**
     * @see https://odoo-restapi.readthedocs.io/en/latest/calling_methods/check_access_rights.html
     *
     * @param string $model
     * @param string $permission see OPERATION_* constants
     * @param bool $withExceptions
     *
     * @return bool
     * @throws AuthException|ResponseException
     *
     * @author Thomas Bondois
     */
    public function check_access_rights(string $model, string $permission = self::OPERATION_READ, bool $withExceptions = false)
    {
        if (!is_array($permission)) {
            $permission = [$permission];
        }
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'check_access_rights',
            $permission,
            ['raise_exception' => $withExceptions]
        );

        //TODO analyse result fault etc
        return (bool)$this->setResponse($response);
    }

    /**
     * Search models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param integer $offset Offset
     * @param integer $limit Max results
     * @param string $order
     * @param array $context Array of context
     *
     * @return array Array of model id's
     * @throws AuthException|ResponseException
     */
    public function search(string $model, array $criteria = [], $offset = 0, $limit = 0, $order = '', array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search',
            [$criteria],
            ['offset' => $offset, 'limit' => $limit, 'order' => $order, 'context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Search_count models
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param array $context Array of context
     *
     * @return int
     * @throws AuthException|ResponseException
     */
    public function search_count(string $model, array $criteria = [], array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search_count',
            [$criteria],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Read model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model (external) id's
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     * @param array $context Array of context
     *
     * @return array An array of models
     * @throws AuthException|ResponseException
     */
    public function read(string $model, array $ids, array $fields = [], array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'read',
            [$ids],
            ['fields' => $fields, 'context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Search and Read model(s)
     *
     * @param string $model Model
     * @param array $criteria Array of criteria
     * @param array $fields Index array of fields to fetch, an empty array fetches all fields
     * @param integer $limit Max results
     * @param string $order
     * @param array $context Array of context
     *
     * @return array An array of models
     * @throws AuthException|ResponseException
     */
    public function search_read(string $model, array $criteria, array $fields = [], int $limit = 0, $order = '', array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'search_read',
            [$criteria],
            [   'fields' => $fields,
                'limit'  => $limit,
                'order'  => $order,
                'context' => $context,
            ]
        );
        return $this->setResponse($response);
    }

    /**
     * @see https://www.odoo.com/documentation/11.0/reference/orm.html#odoo.models.Model.fields_get
     *
     * @param string $model
     * @param array $fields
     * @param array $attributes
     *
     * @return mixed
     * @throws AuthException|ResponseException
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
        return $this->setResponse($response);
    }

    /**
     * Create model
     *
     * @param string $model Model
     * @param array $data Array of fields with data (format: ['field' => 'value'])
     * @param array $context Array of context
     *
     * @return int Created model id
     * @throws AuthException|ResponseException
     */
    public function create(string $model, array $data, array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'create',
            [$data],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Update model(s)
     *
     * @param string $model Model
     * @param array $ids Model ids to update
     * @param array $fields A associative array (format: ['field' => 'value'])
     * @param array $context Array of context
     *
     * @return array
     * @throws AuthException
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function write(string $model, array $ids, array $fields, array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'write',
            [   $ids,
                $fields,
            ],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

    /**
     * Unlink model(s)
     *
     * @param string $model Model
     * @param array $ids Array of model id's
     * @param array $context Array of context
     *
     * @return boolean successful or not
     * @throws AuthException|ResponseException
     */
    public function unlink(string $model, array $ids, array $context = [])
    {
        $response = $this->getModelService()->execute_kw(
            $this->db, $this->uid(), $this->password,
            $model,
            'unlink',
            [$ids],
            ['context' => $context]
        );
        return $this->setResponse($response);
    }

}
