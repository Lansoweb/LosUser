<?php

namespace LosUser\Authentication\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container as SessionContainer;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use LosBase\Entity\EntityManagerAwareTrait;
use Zend\Authentication\Adapter\AdapterInterface;
use LosUser\Options\IdentityOptionsInterface;
use Zend\Authentication\Storage;
use LosBase\EventManager\EventProvider;
use Zend\EventManager\Event;

class Doctrine extends EventProvider implements AdapterInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, EntityManagerAwareTrait;

    protected $options;

    protected $identity;

    protected $password;

    protected $storage;

    public function logout()
    {
        $this->getStorage()->clear();
    }

    public function prepare($identity, $password)
    {
        $this->identity = $identity;
        $this->password = $password;
    }

    public function authenticate()
    {
        $user = null;

        $fields = $this->getOptions()->getIdentityFields();
        $userClass = $this->getOptions()->getUserEntityClass();
        while (! is_object($user) && count($fields) > 0) {
            $mode = array_shift($fields);
            switch ($mode) {
                case 'username':
                    $user = $this->getEntityManager()
                        ->getRepository($userClass)
                        ->findOneByUsername($this->identity);
                    break;
                case 'email':
                    $user = $this->getEntityManager()
                        ->getRepository($userClass)
                        ->findOneByEmail($this->identity);
                    break;
            }
        }

        $e = new AdapterEvent();
        $e->setTarget($this);

        if (! $user) {
            $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND);
            $this->getEventManager()->trigger('authenticate.fail', $e);
            return new AuthenticationResult(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND, null);
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(14);
        if (! $bcrypt->verify($this->password, $user->getPassword())) {
            $e->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID);
            $this->getEventManager()->trigger('authenticate.fail', $e);
            return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID, null);
        }

        $e->setCode(AuthenticationResult::SUCCESS)->setIdentity($user);
        $this->getEventManager()->trigger('authenticate.success', $e);
        return new AuthenticationResult(AuthenticationResult::SUCCESS, $user->getId());
    }

    protected function updateUserPasswordHash($userObject, $password, $bcrypt)
    {
        $hash = explode('$', $userObject->getPassword());
        if ($hash[2] === $bcrypt->getCost()) {
            return;
        }
        $userObject->setPassword($bcrypt->create($password));
        $this->getMapper()->update($userObject);

        return $this;
    }

    public function setOptions(IdentityOptionsInterface $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        if (! $this->options instanceof IdentityOptionsInterface) {
            $this->setOptions($this->getServiceLocator()
                ->get('losuser_module_options'));
        }

        return $this->options;
    }

    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session);
        }

        return $this->storage;
    }

    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }
}
