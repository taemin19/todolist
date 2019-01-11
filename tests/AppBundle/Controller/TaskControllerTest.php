<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\TaskController;
use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class TaskControllerTest extends TestCase
{
    /**
     * This test checks that the method listAction() is correctly returned
     * and checks that the methods render(), getRepository(), findAll() are correctly called.
     *
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testList()
    {
        $objectRepositoryMock = $this->createMock(ObjectRepository::class);
        $objectRepositoryMock->expects($this->once())
            ->method('findAll');

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('getRepository')
            ->with('AppBundle:Task')
            ->willReturn($objectRepositoryMock);

        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($this->once())
            ->method('render')
            ->with('task/list.html.twig', [
                'tasks' => null,
            ]);

        $controller = new TaskController($twigMock, $entityManagerMock);

        $this->assertInstanceOf(Response::class, $controller->listAction());
    }

    /**
     * This test checks that the method createAction() is correctly returned
     * and checks that the methods create(), handleRequest(), isSubmitted(), isValid(),
     * persist(), flush(), add(), generate(), render(), createView() are correctly called or not.
     *
     * @param string $className
     * @param bool   $formIsSubmitted
     *
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @dataProvider provideFormIsSubmitted
     */
    public function testCreate(bool $formIsSubmitted, string $className)
    {
        $requestMock = $this->createMock(Request::class);

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())
            ->method('handleRequest')
            ->with($requestMock);
        $formMock->expects($this->once())
            ->method('isSubmitted')->willReturn($formIsSubmitted);
        $formMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('isValid')->willReturn($formIsSubmitted);
        $formMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('createView');

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock->expects($this->once())
            ->method('create')
            ->with(TaskType::class)
            ->willReturn($formMock);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('persist');
        $entityManagerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('flush');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('add')
            ->with('success', 'La tâche a bien été ajoutée.');

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('generate')
            ->with('task_list')
            ->willReturn('/');

        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('render')
            ->with('task/create.html.twig');

        $controller = new TaskController($twigMock, $entityManagerMock);

        $this->assertInstanceOf($className, $controller->createAction($formFactoryMock, $requestMock, $flashBagMock, $routerMock));
    }

    /**
     * This test checks that the method EditAction() is correctly returned
     * and checks that the methods create(), handleRequest(), isSubmitted(), isValid(),
     * flush(), add(), generate(), render(), createView() are correctly called or not.
     *
     * @param string $className
     * @param bool   $formIsSubmitted
     *
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @dataProvider provideFormIsSubmitted
     */
    public function testEdit(bool $formIsSubmitted, string $className)
    {
        $task = new Task();

        $requestMock = $this->createMock(Request::class);

        $formMock = $this->createMock(FormInterface::class);
        $formMock->expects($this->once())
            ->method('handleRequest')
            ->with($requestMock);
        $formMock->expects($this->once())
            ->method('isSubmitted')->willReturn($formIsSubmitted);
        $formMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('isValid')->willReturn($formIsSubmitted);
        $formMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('createView');

        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock->expects($this->once())
            ->method('create')
            ->with(TaskType::class, $task)
            ->willReturn($formMock);

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('flush');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('add')
            ->with('success', 'La tâche a bien été modifiée.');

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('generate')
            ->with('task_list')
            ->willReturn('/');

        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('render')
            ->with('task/edit.html.twig');

        $controller = new TaskController($twigMock, $entityManagerMock);

        $this->assertInstanceOf($className, $controller->editAction($formFactoryMock, $task, $requestMock, $flashBagMock, $routerMock));
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
     * This test checks that the method ToggleAction() is correctly returned
     * and checks that the methods toggle(), isDone(), flush(), add(), getTile, add(), generate()
     * are correctly called.
     *
     * @throws \ReflectionException
     */
    public function testToggleTask()
    {
        $task = new Task();

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('flush');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())
            ->method('add')
            ->with('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($this->once())
            ->method('generate')
            ->with('task_list')
            ->willReturn('/');

        $twigMock = $this->createMock(\Twig_Environment::class);

        $controller = new TaskController($twigMock, $entityManagerMock);

        $this->assertInstanceOf(RedirectResponse::class, $controller->toggleTaskAction($task, $flashBagMock, $routerMock));
    }

    /**
     * This test checks that the method DeleteAction() is correctly returned
     * and checks that the methods remove(), add(), flush(), generate() are correctly called.
     *
     * @throws \ReflectionException
     */
    public function testDeleteTask()
    {
        $task = new Task();

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($this->once())
            ->method('remove')
            ->with($task);
        $entityManagerMock->expects($this->once())
            ->method('flush');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($this->once())
            ->method('add')
            ->with('success', 'La tâche a bien été supprimée.');

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($this->once())
            ->method('generate')
            ->with('task_list')
            ->willReturn('/');

        $twigMock = $this->createMock(\Twig_Environment::class);

        $controller = new TaskController($twigMock, $entityManagerMock);

        $this->assertInstanceOf(RedirectResponse::class, $controller->deleteTaskAction($task, $flashBagMock, $routerMock));
    }
}
