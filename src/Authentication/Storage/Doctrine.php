<?php

namespace LosUser\Authentication\Storage;

use Zend\Authentication\Storage;
use Zend\Authentication\Storage\StorageInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use LosBase\Entity\EntityManagerAwareTrait;

class Doctrine implements Storage\StorageInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, EntityManagerAwareTrait;

    protected $storage;

    protected $resolvedIdentity;

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
        if (null !== $this->resolvedIdentity) {
            return $this->resolvedIdentity;
        }

        $identity = $this->getStorage()->read();

        if (is_int($identity) || is_scalar($identity)) {
            $identity = $this->getEntityManager()->find($identity);
        }

        if ($identity) {
            $this->resolvedIdentity = $identity;
        } else {
            $this->resolvedIdentity = null;
        }

        return $this->resolvedIdentity;
    }

    public function write($contents)
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->write($contents);
    }

    public function clear()
    {
        $this->resolvedIdentity = null;
        $this->getStorage()->clear();
    }

    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new Storage\Session());
        }

        return $this->storage;
    }

    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }
}
