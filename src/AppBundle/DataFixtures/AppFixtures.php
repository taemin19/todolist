<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var Factory
     */
    private $faker;

    /**
     * AppFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadTasks($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadUsers(ObjectManager $manager)
    {
        // create 2 users
        for ($i = 1; $i <= 2; ++$i) {
            $user = new User();
            $user->setUsername('User'.$i);
            $user->setPassword($this->encoder->encodePassword($user, 'user'.$i));
            $user->setEmail($this->faker->unique()->email);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadTasks(ObjectManager $manager)
    {
        // create 5 tasks
        for ($i = 1; $i <= 5; ++$i) {
            $task = new Task();
            $task->setTitle('Task'.$i);
            $task->setContent($this->faker->text(100));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
