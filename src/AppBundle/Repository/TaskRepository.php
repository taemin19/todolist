<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class TaskRepository.
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * TaskRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param User $user
     *
     * @return Task[]
     */
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.isDone = :isDone')
            ->setParameter('isDone', false)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->useResultCache(true, 3600, 'tasks_all')
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return Task[]
     */
    public function findAllIsDoneByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.isDone = :isDone')
            ->setParameter('isDone', true)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->useResultCache(true, 3600, 'tasks_all_done')
            ->getResult();
    }
}
