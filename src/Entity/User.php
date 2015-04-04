<?php

namespace LosUser\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends AbstractUser
{
    /**
     * @var string
     */
    protected $email;

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
}
