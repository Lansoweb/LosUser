<?php

namespace LosUser\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Authentication\AuthenticationService;
use LosUser\Authentication\Adapter\Doctrine as AuthAdapter;

class LosUserAuthentication extends AbstractPlugin
{
    protected $authAdapter;

    protected $authService;

    /**
     * Proxy convenience method.
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->getAuthService()->hasIdentity();
    }

    /**
     * Proxy convenience method.
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * Get authAdapter.
     *
     * @return ZfcUserAuthentication
     */
    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    /**
     * Set authAdapter.
     *
     * @param authAdapter $authAdapter
     */
    public function setAuthAdapter(AuthAdapter $authAdapter)
    {
        $this->authAdapter = $authAdapter;

        return $this;
    }

    /**
     * Get authService.
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Set authService.
     *
     * @param AuthenticationService $authService
     */
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;

        return $this;
    }
}
