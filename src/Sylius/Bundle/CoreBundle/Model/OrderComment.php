<?php

namespace Sylius\Bundle\CoreBundle\Model;

class OrderComment
{
    protected $id;

    protected $text;

    protected $order;

    protected $user;

    protected $created;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }
}

