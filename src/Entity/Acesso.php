<?php

namespace LosUser\Entity;

use Doctrine\ORM\Mapping as ORM;
use LosBase\Entity\AbstractEntity as AbstractEntity;

class Acesso extends AbstractEntity
{
    /**
     * @ORM\Column(type="string")
     */
    protected $ip;

    /**
     * @ORM\Column(type="string")
     */
    protected $agent;

    /**
     * @ORM\ManyToOne(targetEntity="LosUser\Entity\User", inversedBy="accesses")
     * @ORM\JoinColumn(nullable=false, onDelete="RESTRICT")
     */
    protected $user;

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function setAgent($agent)
    {
        $this->agent = $agent;

        return $this;
    }
}
