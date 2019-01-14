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
     * and checks that the methods are correctly called.
     *
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testList()
    {
        $objectRepository = $this->createMock(ObjectRepository::class);
        $objectRepository->expects($this->once())
            ->method('findAll');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with('AppBundle:User')
            ->willReturn($objectRepository);

        $twig = $this->getTwigMock(true, 'user/list.html.twig', [
            'users' => null,
        ]);

        $controller = new UserController($twig, $entityManager);

        $this->assertInstanceOf(Response::class, $controller->listAction());
    }

    /**
     * This test checks that the method createAction() is correctly returned
     * and checks that the methods are correctly called or not.
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

        $request = $this->createMock(Request::class);

        $form = $this->getFormMock($formIsSubmitted, $request);

        $formFactory = $this->getFormInterfaceMock($form, $user);

        $passwordEncoder = $this->getUserPasswordEncoderMock($formIsSubmitted, $user);

        $entityManager = $this->getEntityManagerMock($formIsSubmitted, false, $formIsSubmitted, $user);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', 'L\'utilisateur a bien été ajouté.');

        $router = $this->getRouterMock($formIsSubmitted, 'user_list');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'user/create.html.twig', [
            'form' => null,
        ]);

        $controller = new UserController($twig, $entityManager);

        $this->assertInstanceOf($className, $controller->createAction($formFactory, $request, $passwordEncoder, $flashBag, $router));
    }

    /**
     * This test checks that the method EditAction() is correctly returned
     * and checks that the methods are correctly called or not.
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

        $request = $this->createMock(Request::class);

        $form = $this->getFormMock($formIsSubmitted, $request);

        $formFactory = $this->getFormInterfaceMock($form, $user);

        $passwordEncoder = $this->getUserPasswordEncoderMock($formIsSubmitted, $user);

        $entityManager = $this->getEntityManagerMock(false, false, $formIsSubmitted, $user);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', 'L\'utilisateur a bien été modifié');

        $router = $this->getRouterMock($formIsSubmitted, 'user_list');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'user/edit.html.twig', [
            'form' => null,
            'user' => $user,
        ]);

        $controller = new UserController($twig, $entityManager);

        $this->assertInstanceOf($className, $controller->editAction($formFactory, $user, $request, $passwordEncoder, $flashBag, $router));
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
        $twigMock = $this->createMock(\Twig_Environment::class);
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
     * @param User $user
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|FormFactoryInterface
     */
    private function getFormInterfaceMock($form, User $user)
    {
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock->expects($this->once())
            ->method('create')
            ->with(UserType::class, $user)
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
     * This helper method mocks UserPasswordEncoderInterface
     * and checks that encodePassword() is correctly called or not called.
     *
     * @param bool $callEncodePassword
     * @param User $user
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|UserPasswordEncoderInterface
     */
    private function getUserPasswordEncoderMock(bool $callEncodePassword, User $user)
    {
        $userPasswordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $userPasswordEncoder->expects($callEncodePassword ? $this->once() : $this->never())
            ->method('encodePassword')
            ->with($user, $user->getPassword());

        return $userPasswordEncoder;
    }

    /**
     * This helper method mocks EntityManagerInterface
     * and checks that persist(), remove(), flush() are correctly called or not called.
     *
     * @param bool $callPersist
     * @param bool $callRemove
     * @param bool $callFlush
     * @param User $user
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface
     */
    private function getEntityManagerMock(bool $callPersist, bool $callRemove, bool $callFlush, User $user)
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $entityManagerMock->expects($callPersist ? $this->once() : $this->never())
            ->method('persist')
            ->with($user);
        $entityManagerMock->expects($callRemove ? $this->once() : $this->never())
            ->method('remove')
            ->with($user);
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
}
