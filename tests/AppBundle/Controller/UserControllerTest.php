<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\UserController;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserControllerTest extends TestCase
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
            ->with('AppBundle:User')
            ->willReturn($objectRepositoryMock);

        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($this->once())
            ->method('render')
            ->with('user/list.html.twig', [
                'users' => null,
            ]);

        $controller = new UserController($twigMock, $entityManagerMock);

        $this->assertInstanceOf(Response::class, $controller->listAction());
    }

    /**
     * This test checks that the method createAction() is correctly returned
     * and checks that the methods create(), handleRequest(), isSubmitted(), isValid(), encodePassword(),
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
        $user = new User();

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
            ->with(UserType::class, $user)
            ->willReturn($formMock);

        $passwordEncoderMock = $this->createMock(UserPasswordEncoderInterface::class);
        $passwordEncoderMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('encodePassword')
            ->with($user, $user->getPassword());

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('persist');
        $entityManagerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('flush');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('add')
            ->with('success', 'L\'utilisateur a bien été ajouté.');

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('generate')
            ->with('user_list')
            ->willReturn('/');

        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('render')
            ->with('user/create.html.twig');

        $controller = new UserController($twigMock, $entityManagerMock);

        $this->assertInstanceOf($className, $controller->createAction($formFactoryMock, $requestMock, $passwordEncoderMock, $flashBagMock, $routerMock));
    }

    /**
     * This test checks that the method EditAction() is correctly returned
     * and checks that the methods create(), handleRequest(), isSubmitted(), isValid(), encodePassword(),
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
        $user = new User();

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
            ->with(UserType::class, $user)
            ->willReturn($formMock);

        $passwordEncoderMock = $this->createMock(UserPasswordEncoderInterface::class);
        $passwordEncoderMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('encodePassword')
            ->with($user, $user->getPassword());

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('flush');

        $flashBagMock = $this->createMock(FlashBagInterface::class);
        $flashBagMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('add')
            ->with('success', 'L\'utilisateur a bien été modifié');

        $routerMock = $this->createMock(RouterInterface::class);
        $routerMock->expects($formIsSubmitted ? $this->once() : $this->never())
            ->method('generate')
            ->with('user_list')
            ->willReturn('/');

        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($formIsSubmitted ? $this->never() : $this->once())
            ->method('render')
            ->with('user/edit.html.twig');

        $controller = new UserController($twigMock, $entityManagerMock);

        $this->assertInstanceOf($className, $controller->editAction($formFactoryMock, $user, $requestMock, $passwordEncoderMock, $flashBagMock, $routerMock));
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
}
