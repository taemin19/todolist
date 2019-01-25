<?php

namespace Tests\AppBundle\Unit\Entity;

use AppBundle\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    /**
     * This test checks that the methods
     * are correctly returned / called.
     */
    public function testMethod()
    {
        $task = new Task();

        $this->assertNull($task->getId());

        $createAt = new \DateTime();
        $task->setCreatedAt($createAt);
        $this->assertSame($createAt, $task->getCreatedAt());

        $task->setTitle('Title');
        $this->assertSame('Title', $task->getTitle());

        $task->setContent('Lorem ipsum dolor sit amet.');
        $this->assertSame('Lorem ipsum dolor sit amet.', $task->getContent());

        $this->assertFalse($task->isDone());

        $task->toggle(true);
        $this->assertTrue($task->isDone());
    }
}
