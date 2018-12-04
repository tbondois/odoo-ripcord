<?php

namespace Ripoo\Handler;

use Ripoo\Service\CommonService;
use Ripoo\Exception\AuthException;
use Ripoo\Exception\ResponseFaultException;
use Ripoo\Exception\ResponseStatusException;

/**
 * Handle methods related to Odoo Common Service/Endpoint
 * @author Thomas Bondois
 */
trait CommonHandler
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
     * @throws AuthException
     * @throws AuthException|ResponseFaultException|ResponseStatusException
     */
    private function uid(bool $reset = false) : int
    {
        if ($reset || null === $this->uid) {

            if (!$this->db || !$this->user || !$this->password) {
                throw new AuthException("Authentication data missing");
            }

            $response = $this->getCommonService()->authenticate(
                $this->db, $this->user, $this->password,
                []
            );

            if (is_int($response)) {
                $this->uid = $response;
            } else {
                $this->formatResponse($response); // can throw more detaild response error exception
                throw new AuthException('Unsuccessful Authentication');
            }
        }
        return $this->uid;
    }

    /**
     * @param bool $reset
     * @return bool
     *
     * @author Thomas Bondois
     */
    public function testAuthenticate(bool $reset = false): bool
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
     * Get version
     *
     * @return array
     * @throws ResponseFaultException|ResponseStatusException
     */
    public function version()
    {
        $response = $this->getCommonService()->version();
        //$response = $this->getCommonService()->version(); // TODO understand why crash Odoo
        return $this->formatResponse($response);
    }

}
