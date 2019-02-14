<?php

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context
{
    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var User
     */
    private $currentUser;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     *
     * @param ManagerRegistry              $doctrine
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(ManagerRegistry $doctrine, UserPasswordEncoderInterface $encoder)
    {
        $manager = $doctrine->getManager();
        $this->schemaTool = new SchemaTool($manager);
        $this->classes = $manager->getMetadataFactory()->getAllMetadata();
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    /**
     * @AfterScenario
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function clearDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
        $this->doctrine->getManager()->clear();
        $this->schemaTool->createSchema($this->classes);
    }

    /**
     * @BeforeScenario @loginAsUserNick
     */
    public function iAmLoggedInAsUser()
    {
        $user = new User();
        $user->setUsername('nick');
        $user->setPassword($this->encoder->encodePassword($user, 'shield'));
        $user->setEmail('nick@fury.com');

        $this->currentUser = $user;

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        $this->visitPath('/login');
        $this->fillField('username', 'nick');
        $this->fillField('password', 'shield');
        $this->pressButton('Connexion');
    }

    /**
     * @BeforeScenario @loginAsAdminShield
     */
    public function iAmLoggedInAsAdmin()
    {
        $user = new User();
        $user->setUsername('shield');
        $user->setPassword($this->encoder->encodePassword($user, 'avengers'));
        $user->setEmail('the@avengers.com');
        $user->setRoles(['ROLE_ADMIN']);

        $this->currentUser = $user;

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        $this->visitPath('/login');
        $this->fillField('username', 'shield');
        $this->fillField('password', 'avengers');
        $this->pressButton('Connexion');
    }

    /**
     * @param TableNode $table
     * @Given the following users exist:
     */
    public function theFollowingUsersExist(TableNode $table)
    {
        $em = $this->doctrine->getManager();

        foreach ($table->getHash() as $userHash) {
            $user = new User();
            $user->setUsername($userHash['username']);
            $user->setPassword($this->encoder->encodePassword($user, $userHash['password']));
            $user->setEmail($userHash['email']);
            $em->persist($user);
        }

        $em->flush();
    }

    /**
     * @param TableNode $table
     * @Given the following admins exist:
     */
    public function theFollowingAdminsExist(TableNode $table)
    {
        $em = $this->doctrine->getManager();

        foreach ($table->getHash() as $userHash) {
            $user = new User();
            $user->setUsername($userHash['username']);
            $user->setPassword($this->encoder->encodePassword($user, $userHash['password']));
            $user->setEmail($userHash['email']);
            $user->setRoles(['ROLE_ADMIN']);
            $em->persist($user);
        }

        $em->flush();
    }

    /**
     * @param TableNode $table
     * @Given the following tasks exist:
     */
    public function theFollowingTasksExist(TableNode $table)
    {
        $em = $this->doctrine->getManager();

        foreach ($table->getHash() as $taskHash) {
            $task = new Task();
            $task->setTitle($taskHash['title']);
            $task->setContent($taskHash['content']);
            $task->toggle($taskHash['isDone']);
            $em->persist($task);
        }

        $em->flush();
    }

    /**
     * @param TableNode $table
     * @Given the following tasks exist for current user:
     */
    public function theFollowingTasksExistForCurrentUser(TableNode $table)
    {
        $em = $this->doctrine->getManager();

        foreach ($table->getHash() as $taskHash) {
            $task = new Task();
            $task->setTitle($taskHash['title']);
            $task->setContent($taskHash['content']);
            $task->toggle($taskHash['isDone']);
            $task->setUser($this->currentUser);

            $em->merge($task);
        }

        $em->flush();
    }

    /**
     * @param TableNode $table
     * @Given the following tasks exist for user Tony:
     */
    public function theFollowingTasksExistForUserTony(TableNode $table)
    {
        $em = $this->doctrine->getManager();

        $user = new User();
        $user->setUsername('tony');
        $user->setPassword($this->encoder->encodePassword($user, 'ironman'));
        $user->setEmail('tony@stark.com');
        $em->persist($user);

        foreach ($table->getHash() as $taskHash) {
            $task = new Task();
            $task->setTitle($taskHash['title']);
            $task->setContent($taskHash['content']);
            $task->toggle($taskHash['isDone']);
            $task->setUser($user);

            $em->persist($task);
        }

        $em->flush();
    }

    /**
     * @When /^(?:|I )click "(?P<link>(?:[^"]|\\")*)"$/
     *
     * @param $link
     */
    public function iClick($link)
    {
        $this->clickLink($link);
    }

    /**
     * @Then /^(?:|I )should see (?P<num>\d+) users?$/
     *
     * @param $num
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iShouldSeeUsers($num)
    {
        $this->assertSession()->elementsCount('css', 'tbody tr', (int) $num);
    }

    /**
     * @Then /^(?:|I )should see (?P<num>\d+) tasks?$/
     *
     * @param $num
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iShouldSeeTasks($num)
    {
        $this->assertSession()->elementsCount('css', '.card', (int) $num);
    }

    /**
     * @Then /^(?:|I )should see (?P<num>\d+) tasks to do?$/
     *
     * @param $num
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iShouldSeeTasksToDo($num)
    {
        $this->assertSession()->elementsCount('css', '.fa-square', (int) $num);
    }

    /**
     * @Then /^(?:|I )should see (?P<num>\d+) done tasks?$/
     *
     * @param $num
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function iShouldSeeDoneTasks($num)
    {
        $this->assertSession()->elementsCount('css', '.fa-check-square', (int) $num);
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )break
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while ('' === fgets(STDIN, 1024)) {
        }
        fwrite(STDOUT, "\033[u");
    }
}
