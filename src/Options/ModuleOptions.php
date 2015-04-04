<?php

namespace LosUser\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements ControllerOptionsInterface, ServiceOptionsInterface, IdentityOptionsInterface
{
    protected $__strictMode__ = false;

    protected $useRedirect = true;

    protected $loginRedirectRoute = 'losuser';

    protected $logoutRedirectRoute = 'losuser/login';

    protected $identityFields = array(
        'email',
    );

    protected $userEntityClass = 'LosUser\Entity\User';

    protected $enableUsername = false;

    public function setLoginRedirectRoute($loginRedirectRoute)
    {
        $this->loginRedirectRoute = $loginRedirectRoute;

        return $this;
    }

    public function getLoginRedirectRoute()
    {
        return $this->loginRedirectRoute;
    }

    public function setLogoutRedirectRoute($logoutRedirectRoute)
    {
        $this->logoutRedirectRoute = $logoutRedirectRoute;

        return $this;
    }

    public function getLogoutRedirectRoute()
    {
        return $this->logoutRedirectRoute;
    }

    public function setUseRedirect($useRedirect)
    {
        $this->useRedirect = $useRedirect;

        return $this;
    }

    public function getUseRedirect()
    {
        return $this->useRedirect;
    }

    public function setIdentityFields($identityFields)
    {
        $this->identityFields = $identityFields;

        return $this;
    }

    public function getIdentityFields()
    {
        return $this->identityFields;
    }

    public function setEnableUsername($flag)
    {
        $this->enableUsername = (bool) $flag;

        return $this;
    }

    public function getEnableUsername()
    {
        return $this->enableUsername;
    }

    public function setUserEntityClass($userEntityClass)
    {
        $this->userEntityClass = $userEntityClass;

        return $this;
    }

    public function getUserEntityClass()
    {
        return $this->userEntityClass;
    }
}
