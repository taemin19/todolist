<?php

namespace Tests\AppBundle\Unit\Controller;

use AppBundle\Controller\TaskController;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskControllerTest extends TestCase
{
    /**
     * This test checks that the method listAction() is correctly returned
     * and checks that the methods are correctly called.
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testList()
    {
        $tokenStorage = $this->getTokenStorageMock();

        $taskRepository = $this->getTaskRepositoryMock('findAllByUser');

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $twig = $this->getTwigMock(true, 'task/list.html.twig', [
            'tasks' => [],
        ]);

        $controller = new TaskController($twig, $entityManager);

        $this->assertInstanceOf(Response::class, $controller->listAction($tokenStorage, $taskRepository));
    }

    /**
     * This test checks that the method listDoneAction() is correctly returned
     * and checks that the methods are correctly called.
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testListDone()
    {
        $tokenStorage = $this->getTokenStorageMock();

        $taskRepository = $this->getTaskRepositoryMock('findAllIsDoneByUser');

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $twig = $this->getTwigMock(true, 'task/list_done.html.twig', [
            'tasks' => [],
        ]);

        $controller = new TaskController($twig, $entityManager);

        $this->assertInstanceOf(Response::class, $controller->listDoneAction($tokenStorage, $taskRepository));
    }

    /**
     * This test checks that the method createAction() is correctly returned
     * and checks that the methods are correctly called or not.
     *
     * @param bool   $formIsSubmitted
     * @param string $className
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @dataProvider provideFormIsSubmitted
     */
    public function testCreate(bool $formIsSubmitted, string $className)
    {
        $task = new Task();

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $request = $this->createMock(Request::class);

        $form = $this->getFormMock($formIsSubmitted, $request);

        $formFactory = $this->getFormInterfaceMock($form);

        $entityManager = $this->getEntityManagerMock($formIsSubmitted, false, $formIsSubmitted, $task);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', 'La tâche a bien été ajoutée.');

        $router = $this->getRouterMock($formIsSubmitted, 'task_list');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'task/create.html.twig', [
            'form' => null,
        ]);

        $controller = new TaskController($twig, $entityManager);

        $this->assertInstanceOf($className, $controller->createAction($tokenStorage, $formFactory, $request, $flashBag, $router));
    }

    /**
     * This test checks that the method editAction() is correctly returned
     * and checks that the methods are correctly called or not.
     *
     * @param bool   $formIsSubmitted
     * @param string $className
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @dataProvider provideFormIsSubmitted
     */
    public function testEdit(bool $formIsSubmitted, string $className)
    {
        $task = new Task();

        $request = $this->createMock(Request::class);

        $form = $this->getFormMock($formIsSubmitted, $request);

        $formFactory = $this->getFormInterfaceMock($form);

        $entityManager = $this->getEntityManagerMock(false, false, $formIsSubmitted, $task);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', 'La tâche a bien été modifiée.');

        $router = $this->getRouterMock($formIsSubmitted, 'task_list');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'task/edit.html.twig', [
            'form' => null,
            'task' => $task,
        ]);

        $controller = new TaskController($twig, $entityManager);

        $this->assertInstanceOf($className, $controller->editAction($formFactory, $task, $request, $flashBag, $router));
    }

    /**
     * @return array
     */
    public function provideFormIsSubmitted()
    {
        return [
            [true, RedirectResponse::class],
            [false, Response::class],
        ];
    }

    /**
     * This test checks that the method toggleAction() is correctly returned
     * and checks that the methods are correctly called.
     *
     * @param bool   $taskIsDone
     * @param string $message
     * @param string $route
     *
     * @throws \ReflectionException
     * @dataProvider provideTaskIsDone
     */
    public function testToggleTask(bool $taskIsDone, string $message, string $route)
    {
        $task = $this->createMock(Task::class);

        $task->method('isDone')->willReturn($taskIsDone);

        $entityManager = $this->getEntityManagerMock(false, false, true, $task);

        $flashBag = $this->getFlashBagMock(true, 'success', sprintf($message, ''));

        $router = $this->getRouterMock(true, $route);

        $twig = $this->getTwigMock(false);

        $controller = new TaskController($twig, $entityManager);

        $this->assertInstanceOf(RedirectResponse::class, $controller->toggleTaskAction($task, $flashBag, $router));
    }

    /**
     * @return array
     */
    public function provideTaskIsDone()
    {
        return [
            [true, 'La tâche %s a bien été marquée comme faite.', 'task_list'],
            [false, 'La tâche %s a bien été marquée comme non terminée.', 'task_done'],
        ];
    }

    /**
     * This test checks that the method deleteAction() is correctly returned
     * and checks that the methods are correctly called.
     *
     * @throws \ReflectionException
     */
    public function testDeleteTask()
    {
        $task = new Task();

        $entityManager = $this->getEntityManagerMock(false, true, true, $task);

        $flashBag = $this->getFlashBagMock(true, 'success', 'La tâche a bien été supprimée.');

        $router = $this->getRouterMock(true, 'task_list');

        $twig = $this->getTwigMock(false);

        $controller = new TaskController($twig, $entityManager);

        $this->assertInstanceOf(RedirectResponse::class, $controller->deleteTaskAction($task, $flashBag, $router));
    }

    /**
     * This helper method mocks Twig_Environment
     * and checks that render() is correctly called or not called.
     *
     * @param bool   $callRender
     * @param string $template
     * @param array  $parameters
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Twig_Environment
     */
    private function getTwigMock(bool $callRender, string $template = '', array $parameters = [])
    {
        $twigMock = $this->createMock(\Twig\Environment::class);
        $twigMock->expects($callRender ? $this->once() : $this->never())
            ->method('render')
            ->with($template, $parameters);

        return $twigMock;
    }

    /**
     * This helper method mocks FormFactoryInterface
     * and checks that create() is correctly called.
     *
     * @param $form
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|FormFactoryInterface
     */
    private function getFormInterfaceMock($form)
    {
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock->expects($this->once())
            ->method('create')
            ->with(TaskType::class)
            ->willReturn($form);

        return $formFactoryMock;
    }

    /**
     * This helper method mocks FormInterface
     * and checks that handleRequest(), isSubmitted(), isValid(), createView() are correctly called or not called.
     *
     * @param bool $formIsSubmitted
     * @param $request
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|FormInterface
     */
    private function getFormMock(bool $formIsSubmitted, $request)
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())
            ->method('handleRequest')
            ->with($request);
        $formMock->expects($this->once())
            ->method('isSubmitted')->willReturn($formIsSubmitted);
        $formMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('isValid')->willReturn($formIsSubmitted);
        $formMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('createView');

        return $formMock;
    }

    /**
     * This helper method mocks EntityManagerInterface
     * and checks that persist(), remove(), flush() are correctly called or not called.
     *
     * @param bool $callPersist
     * @param bool $callRemove
     * @param bool $callFlush
     * @param Task $task
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface
     */
    private function getEntityManagerMock(bool $callPersist, bool $callRemove, bool $callFlush, Task $task)
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($callPersist ? $this->once() : $this->never())
            ->method('persist');
        $entityManagerMock->expects($callRemove ? $this->once() : $this->never())
            ->method('remove')
            ->with($task);
        $entityManagerMock->expects($callFlush ? $this->once() : $this->never())
            ->method('flush');

        return $entityManagerMock;
    }

    /**
     * This helper method mocks FlashBagInterface
     * and checks that add() is correctly called or not called.
     *
     * @param bool   $callAdd
     * @param string $type
     * @param string $message
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|FlashBagInterface
     */
    private function getFlashBagMock(bool $callAdd, string $type, string $message)
    {
        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($callAdd ? $this->once() : $this->never())
            ->method('add')
            ->with($type, $message);

        return $flashBagMock;
    }

    /**
     * This helper method mocks RouterInterface
     * and checks that generate() is correctly called or not called.
     *
     * @param bool   $callGenerate
     * @param string $route
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|RouterInterface
     */
    private function getRouterMock(bool $callGenerate, string $route)
    {
        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($callGenerate ? $this->once() : $this->never())
            ->method('generate')
            ->with($route)
            ->willReturn('/');

        return $routerMock;
    }

    /**
     * This helper method mocks TokenInterface|TokenStorageInterface
     * and checks that getUser()|getToken() is correctly called.
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|TokenStorageInterface
     */
    private function getTokenStorageMock()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        return $tokenStorage;
    }

    /**
     * This helper method mocks TaskRepository
     * and checks that a method is correctly called.
     *
     * @param string $method
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|TaskRepository
     */
    private function getTaskRepositoryMock(string $method)
    {
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskRepository->expects($this->once())
            ->method($method)
            ->with(new User());

        return $taskRepository;
    }
}
