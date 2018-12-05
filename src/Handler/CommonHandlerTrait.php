<?php

namespace Ripoo\Handler;

use Ripoo\Service\CommonService;
use Ripoo\Exception\AuthException;
use Ripoo\Exception\ResponseFaultException;

/**
 * Handle methods related to Odoo Common Service/Endpoint
 * @author Thomas Bondois
 */
trait CommonHandlerTrait
{
    /**
     * odoo.service.common.dispatch
     * @see https://github.com/odoo/odoo/blob/11.0/odoo/service/common.py
     *
     * TODO understand why odoo return fault (UnboundLocalError: local variable 'dispatch' referenced before assignment),
     * if we use getCommonService()->version, and no fault if we we getService('commmon')->version()
     *
     * @return CommonService
     */
    public function getCommonService() : CommonService
    {
        return $this->getService(self::ENDPOINT_COMMON);
    }

    /**
     * Get uid
     *
     * @param bool $reset
     * @return int $uid
     * @throws AuthException|ResponseFaultException
     */
    private function uid(bool $reset = false) : int
    {
        if ($reset || null === $this->uid) {

            if (null === $this->db || null === $this->user || null === $this->password) {
                throw new AuthException("Authentication data missing");
            }

            $response = $this->getCommonService()->authenticate(
                $this->db, $this->user, $this->password,
                []
            );

            if (is_int($response)) {
                $this->uid = $response;
            } else {
                $this->checkResponse($response); // can throw more detaild response error exception
                throw new AuthException('Unsuccessful Authentication');
            }
        }
        return $this->uid;
    }

    /**
     * @param bool $reset
     * @return bool
     */
    public function tryAuthenticate(bool $reset = false): bool
    {
        try {
            if ($this->uid($reset)) {
                return true;
            }
        } catch (\Throwable $e) {
        }
        return false;
    }

    /**
     * @param bool $reset
     * @return bool
     * @throws AuthException|ResponseFaultException
     */
    public function testAuthenticate(bool $reset = false) : bool
    {
        return (bool)$this->uid($reset);
    }

    /**
     * Get version
     *
     * @return array
     * @throws ResponseException|ResponseFaultException
     */
    public function version()
    {
        $response = $this->getCommonService()->version();
        //$response = $this->getCommonService()->version(); // TODO understand why crash Odoo
        return $this->checkResponse($response);
    }

}
