<?php

namespace Tests\AppBundle\Integration\Repository;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryIntegrationTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->encoder = $kernel->getContainer()
            ->get('security.password_encoder');
    }

    /**
     * This test checks that findAllByUser query
     * is correctly executed.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testFindAllByUser(): void
    {
        $user = $this->getUser();
        $this->entityManager->persist($user);

        $task = $this->getTask();
        $task->setUser($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush($task);

        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAllByUser($user)
        ;

        $this->assertCount(1, $tasks);
        $this->assertSame('The Avengers', $task->getTitle());
        $this->assertSame('Must defeat Thanos!', $task->getContent());
        $this->assertFalse($task->isDone());
    }

    /**
     * This test checks that findAllIsDoneByUser query
     * is correctly executed.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testFindAllIsDoneByUser(): void
    {
        $user = $this->getUser();
        $this->entityManager->persist($user);

        $task = $this->getTask();
        $task->toggle(true);
        $task->setUser($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush($task);

        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAllIsDoneByUser($user)
        ;

        $this->assertCount(1, $tasks);
        $this->assertSame('The Avengers', $task->getTitle());
        $this->assertSame('Must defeat Thanos!', $task->getContent());
        $this->assertTrue($task->isDone());
    }

    /**
     * @return User
     */
    private function getUser(): User
    {
        $user = new User();
        $user->setUsername('Nick');
        $user->setPassword($this->encoder->encodePassword($user, 'shield'));
        $user->setEmail('nick@fury.com');

        return $user;
    }

    /**
     * @return Task
     */
    private function getTask(): Task
    {
        $task = new Task();
        $task->setTitle('The Avengers');
        $task->setContent('Must defeat Thanos!');

        return $task;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
