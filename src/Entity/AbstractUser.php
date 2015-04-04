<?php

namespace LosUser\Entity;

use Doctrine\ORM\Mapping as ORM;
use LosBase\Entity\Db\Field\Id;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractUser implements UserInterface
{
    use Id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
