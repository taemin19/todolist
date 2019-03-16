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
        $this->loadAdmins($manager);
        $this->loadTasks($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadUsers(ObjectManager $manager)
    {
        // create 2 users: User1 with password 'user1' and User2 with password 'user2'
        for ($i = 1; $i <= 2; ++$i) {
            $user = new User();
            $user->setUsername('User'.$i);
            $user->setPassword($this->encoder->encodePassword($user, 'user'.$i));
            $user->setEmail($this->faker->unique()->email);

            $manager->persist($user);

            // create 6 tasks for User1 and 6 tasks for User2
            for ($j = 1; $j <= 6; ++$j) {
                $task = new Task();
                $task->setTitle('User'.$i.'Task'.$j);
                $task->setContent($this->faker->text(100));
                $task->setUser($user);

                $manager->persist($task);
            }

            // create 6 tasks as done for User1 and 6 tasks as done for User2
            for ($j = 7; $j <= 12; ++$j) {
                $task = new Task();
                $task->setTitle('User'.$i.'Task'.$j);
                $task->setContent($this->faker->text(100));
                $task->toggle(true);
                $task->setUser($user);

                $manager->persist($task);
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadAdmins(ObjectManager $manager)
    {
        // create 2 admins: Admin1 with password 'admin1' and Admin2 with password 'admin2'
        for ($i = 1; $i <= 2; ++$i) {
            $admin = new User();
            $admin->setUsername('Admin'.$i);
            $admin->setPassword($this->encoder->encodePassword($admin, 'admin'.$i));
            $admin->setEmail($this->faker->unique()->email);
            $admin->setRoles(['ROLE_ADMIN']);

            $manager->persist($admin);

            // create 6 tasks for Admin1 and 6 tasks for Admin2
            for ($j = 1; $j <= 6; ++$j) {
                $task = new Task();
                $task->setTitle('Admin'.$i.'Task'.$j);
                $task->setContent($this->faker->text(100));
                $task->setUser($admin);

                $manager->persist($task);
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadTasks(ObjectManager $manager)
    {
        // create 5 tasks not attached to a user
        for ($i = 1; $i <= 5; ++$i) {
            $task = new Task();
            $task->setTitle('Task'.$i);
            $task->setContent($this->faker->text(100));

            $manager->persist($task);
        }

        $manager->flush();
    }
}
