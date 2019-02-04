<?php

namespace AppBundle\Security;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to tasks (such as
 * editing and deleting tasks).
 */
class TaskVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // this voter is only executed for two specific permissions on Task objects
        if (!\in_array($attribute, [self::EDIT, self::DELETE], true)) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $task, TokenInterface $token)
    {
        $user = $token->getUser();
        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }
        // if the logged user is the author of the given task, grant permission; otherwise, deny it.
        return $user === $task->getUser();
    }
}
