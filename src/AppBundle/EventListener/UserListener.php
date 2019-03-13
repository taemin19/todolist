<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserListener
{
    private $cacheDriver;

    /**
     * UserListener constructor.
     *
     * @param $cacheDriver
     */
    public function __construct($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    /**
     * @param User               $user
     * @param LifecycleEventArgs $args
     */
    public function postPersist(User $user, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[users_all][1]', 0);
    }

    /**
     * @param User               $user
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(User $user, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[users_all][1]', 0);
    }
}
