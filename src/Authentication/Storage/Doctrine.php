<?php

namespace LosUser\Authentication\Storage;

use Zend\Authentication\Storage;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use LosBase\Entity\EntityManagerAwareTrait;
use Doctrine\ORM\UnitOfWork;

class Doctrine implements Storage\StorageInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, EntityManagerAwareTrait;

    protected $storage;

    public function isEmpty()
    {
        if ($this->getStorage()->isEmpty()) {
            return true;
        }
        $identity = $this->read();
        if ($identity === null) {
            $this->clear();

            return true;
        }

        return false;
    }

    public function read()
    {
        $identity = $this->getStorage()->read();

        if (is_int($identity) || is_scalar($identity)) {
            $options = $this->getServiceLocator()->get('losuser_module_options');
            $userClass = $options->getUserEntityClass();
            $identity = $this->getEntityManager()->find($userClass, $identity);
        } elseif ($this->getEntityManager()->getUnitOfWork()->getEntityState($identity) === UnitOfWork::STATE_DETACHED) {
            $identity = $this->getEntityManager()->merge($identity);
        }

        return $identity;
    }

    public function write($identity)
    {
        $this->getStorage()->write($identity);
    }

    public function clear()
    {
        $this->getStorage()->clear();
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
