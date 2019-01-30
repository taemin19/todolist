<?php

namespace Tests\AppBundle\Unit\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Task
     */
    private $task;

    public function setUp()
    {
        $this->user = new User();
        $this->task = new Task();
    }

    /**
     * This test checks that the methods
     * are correctly returned / called.
     */
    public function testMethod()
    {
        $this->assertNull($this->user->getId());

        $this->user->setUsername('user');
        $this->assertSame('user', $this->user->getUsername());

        $this->assertNull($this->user->getSalt());

        $this->user->setPassword('password');
        $this->assertSame('password', $this->user->getPassword());

        $this->user->setEmail('user@email.com');
        $this->assertSame('user@email.com', $this->user->getEmail());

        $this->assertSame(['ROLE_USER'], $this->user->getRoles());

        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertSame(['ROLE_ADMIN'], $this->user->getRoles());

        $this->user->addTask($this->task);
        $this->assertNotEmpty($this->user->getTasks());
        $this->assertContainsOnlyInstancesOf(Task::class, $this->user->getTasks());

        $this->user->removeTask($this->task);
        $this->assertEmpty($this->user->getTasks());
    }
}
