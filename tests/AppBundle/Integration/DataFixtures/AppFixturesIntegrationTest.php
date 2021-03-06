<?php

namespace Tests\AppBundle\Integration\DataFixtures;

use AppBundle\DataFixtures\AppFixtures;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixturesIntegrationTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * This test checks that fixtures
     * are correctly created and saved in the database.
     */
    public function testLoad()
    {
        $this->executeLoad();

        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAll();

        $this->assertSame(4, \count($users));
        $this->assertSame(41, \count($tasks));
    }

    /**
     * This test checks that task fixtures
     * are correctly created and saved in the database.
     */
    public function testLoadTasks()
    {
        $this->executeLoad('Tasks');

        $tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findAll();

        $this->assertSame(5, \count($tasks));
    }

    /**
     * This test checks that user fixtures
     * are correctly created and saved in the database.
     */
    public function testLoadUsers()
    {
        $this->executeLoad('Users');

        $users = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $user1 = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'username' => 'User1',
            ]);

        $user1Tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy([
                'user' => $user1,
            ]);

        $user2 = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'username' => 'User2',
            ]);

        $user2Tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy([
                'user' => $user2,
            ]);

        $this->assertSame(2, \count($users));
        $this->assertSame(12, \count($user1Tasks));
        $this->assertSame(12, \count($user2Tasks));
    }

    /**
     * This test checks that admin fixtures
     * are correctly created and saved in the database.
     */
    public function testLoadAdmins()
    {
        $this->executeLoad('Admins');

        $admins = $this->entityManager
            ->getRepository(User::class)
            ->findAll();

        $admin1 = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'username' => 'Admin1',
            ]);

        $admin1Tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy([
                'user' => $admin1,
            ]);

        $admin2 = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'username' => 'Admin2',
            ]);

        $admin2Tasks = $this->entityManager
            ->getRepository(Task::class)
            ->findBy([
                'user' => $admin2,
            ]);

        $this->assertSame(2, \count($admins));
        $this->assertSame(6, \count($admin1Tasks));
        $this->assertSame(6, \count($admin2Tasks));
    }

    /**
     * This helper method abstracts the boilerplate code needed
     * to execute fixtures or a specific fixtures.
     *
     * @param string|null $name
     */
    private function executeLoad(string $name = null)
    {
        $container = self::$kernel->getContainer();

        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = $container->get('security.password_encoder');

        $fixtures = new AppFixtures($encoder);

        $objectManager = $container->get('doctrine.orm.entity_manager');

        $loadFixtures = 'load'.$name;
        $fixtures->$loadFixtures($objectManager);
    }
}
