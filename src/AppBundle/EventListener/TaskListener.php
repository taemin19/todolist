<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Task;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class TaskListener
{
    private $cacheDriver;

    /**
     * TaskListener constructor.
     *
     * @param $cacheDriver
     */
    public function __construct($cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }

    /**
     * @param Task               $task
     * @param LifecycleEventArgs $args
     */
    public function postPersist(Task $task, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[tasks_all][1]', 0);
        $this->cacheDriver->expire('[tasks_all_done][1]', 0);
    }

    /**
     * @param Task               $task
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(Task $task, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[tasks_all][1]', 0);
        $this->cacheDriver->expire('[tasks_all_done][1]', 0);
    }

    /**
     * @param Task               $task
     * @param LifecycleEventArgs $args
     */
    public function postRemove(Task $task, LifecycleEventArgs $args)
    {
        $this->cacheDriver->expire('[tasks_all][1]', 0);
        $this->cacheDriver->expire('[tasks_all_done][1]', 0);
    }
}
