<?php

namespace Tests\AppBundle\Integration\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserIntegrationTest extends KernelTestCase
{
    public function setUp()
    {
        self::bootKernel();
    }

    /**
     * This test checks that a User
     * is correctly created and saved in the database.
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function testCreate()
    {
        $userData = new User();
        $userData->setUsername('nick');
        $userData->setPassword('avengers');
        $userData->setEmail('nick@fury.com');

        $container = self::$kernel->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');
        $em->persist($userData);
        $em->flush();

        $user = $container->get('doctrine')
            ->getRepository(User::class)->find(1);

        $this->assertNotNull($user);
        $this->assertSame('nick', $user->getUsername());
        $this->assertSame('nick@fury.com', $user->getEmail());
        $this->assertSame('avengers', $user->getPassword());
    }
}
