<?php

namespace Tests\AppBundle\Unit\Security;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Security\TaskVoter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoterTest extends TestCase
{
    /**
     * This test checks that the Voter handle all the cases properly.
     *
     * @param string    $attribute
     * @param Task|null $task
     * @param User|null $user
     * @param $expectedVote
     *
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, ?Task $task, ?User $user, $expectedVote)
    {
        $voter = new TaskVoter();

        $token = new AnonymousToken('secret', 'anonymous');
        if ($user) {
            if ($user->getId() === $task->getUser()->getId()) {
                $token = new UsernamePasswordToken(
                    $task->getUser(), 'credentials', 'memory'
                );
            } else {
                $token = new UsernamePasswordToken(
                    $user, 'credentials', 'memory'
                );
            }
        }

        $this->assertSame(
            $expectedVote,
            $voter->vote($token, $task, [$attribute])
        );
    }

    /**
     * @throws \ReflectionException
     *
     * @return array
     */
    public function provideCases()
    {
        return [
            'attribute is not supported' => ['', null, null, Voter::ACCESS_ABSTAIN],
            'not an instance of Task' => ['edit', null, null, Voter::ACCESS_ABSTAIN],
            'anonymous cannot edit' => ['edit', $this->createTask(1), null, Voter::ACCESS_DENIED],
            'anonymous cannot delete' => ['delete', $this->createTask(1), null, Voter::ACCESS_DENIED],
            'non-owner cannot edit' => ['edit', $this->createTask(1), $this->createUser(2), Voter::ACCESS_DENIED],
            'non-owner cannot delete' => ['delete', $this->createTask(1), $this->createUser(2), Voter::ACCESS_DENIED],
            'owner can edit' => ['edit', $this->createTask(1), $this->createUser(1), Voter::ACCESS_GRANTED],
            'owner can delete' => ['delete', $this->createTask(1), $this->createUser(1), Voter::ACCESS_GRANTED],
        ];
    }

    /**
     * @param int $id
     *
     * @throws \ReflectionException
     *
     * @return MockObject|User
     */
    private function createUser(int $id): MockObject
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);

        return $user;
    }

    /**
     * @param int $userId
     *
     * @throws \ReflectionException
     *
     * @return Task
     */
    private function createTask(int $userId)
    {
        $task = new Task();
        $task->setUser($this->createUser($userId));

        return $task;
    }
}
