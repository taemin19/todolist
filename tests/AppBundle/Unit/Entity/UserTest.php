<?php

namespace Tests\AppBundle\Unit\Entity;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * This test checks that the methods
     * are correctly returned / called.
     */
    public function testMethod()
    {
        $user = new User();

        $this->assertNull($user->getId());

        $user->setUsername('user');
        $this->assertSame('user', $user->getUsername());

        $this->assertNull($user->getSalt());

        $user->setPassword('password');
        $this->assertSame('password', $user->getPassword());

        $user->setEmail('user@email.com');
        $this->assertSame('user@email.com', $user->getEmail());

        $this->assertSame(['ROLE_USER'], $user->getRoles());

        $user->addTask(new Task());
        $this->assertNotEmpty($user->getTasks());
        $this->assertContainsOnlyInstancesOf(Task::class, $user->getTasks());
    }
}
