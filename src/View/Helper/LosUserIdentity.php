<?php

namespace LosUser\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

class LosUserIdentity extends AbstractHelper
{
    protected $authService;

    public function __invoke()
    {
        if ($this->getAuthService()->hasIdentity()) {
            return $this->getAuthService()->getIdentity();
        } else {
            return false;
        }
    }

    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;

        return $this;
    }
}
