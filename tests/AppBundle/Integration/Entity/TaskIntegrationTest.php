<?php

namespace Tests\AppBundle\Integration\Entity;

use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskIntegrationTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();
    }

    /**
     * This test checks that a Task
     * is correctly created and saved in the database.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreate()
    {
        $taskData = new Task();
        $taskData->setTitle('The Avengers');
        $taskData->setContent('Must defeat Thanos!');

        $container = self::$kernel->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');
        $em->persist($taskData);
        $em->flush();

        $task = $container->get('doctrine')
            ->getRepository(Task::class)->find(1);

        $this->assertNotNull($task);
        $this->assertSame('The Avengers', $task->getTitle());
        $this->assertSame('Must defeat Thanos!', $task->getContent());
    }
}
