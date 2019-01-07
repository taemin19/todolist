<?php

namespace Tests\AppBundle\DataFixtures;

use AppBundle\DataFixtures\AppFixtures;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixturesTest extends KernelTestCase
{
    /**
     * This test checks that fixtures
     * are correctly created and saved in the database.
     */
    public function testLoad()
    {
        $this->executeLoad();

        $container = self::$kernel->getContainer();

        $users = $container->get('doctrine')
            ->getRepository(User::class)->findAll();

        $tasks = $container->get('doctrine')
            ->getRepository(Task::class)->findAll();

        $this->assertSame(2, \count($users));
        $this->assertSame(5, \count($tasks));
    }

    /**
     * This test checks that task fixtures
     * are correctly created and saved in the database.
     */
    public function testLoadTasks()
    {
        $this->executeLoad('Tasks');

        $container = self::$kernel->getContainer();

        $tasks = $container->get('doctrine')
            ->getRepository(Task::class)->findAll();

        $this->assertSame(5, \count($tasks));
    }

    /**
     * This test checks that user fixtures
     * are correctly created and saved in the database.
     */
    public function testLoadUsers()
    {
        $this->executeLoad('Users');

        $container = self::$kernel->getContainer();

        $users = $container->get('doctrine')
            ->getRepository(User::class)->findAll();

        $this->assertSame(2, \count($users));
    }

    /**
     * This helper method abstracts the boilerplate code needed
     * to execute fixtures or a specific fixtures.
     *
     * @param string|null $name
     */
    private function executeLoad(string $name = null)
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();

        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = $container->get('security.password_encoder');

        $fixtures = new AppFixtures($encoder);

        $objectManager = $container->get('doctrine.orm.entity_manager');

        $loadFixtures = 'load'.$name;
        $fixtures->$loadFixtures($objectManager);
    }
}
