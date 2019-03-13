<?php

namespace Tests\AppBundle\Unit\Controller;

use AppBundle\Controller\Admin\UserAdminController;
use AppBundle\Entity\User;
use AppBundle\Form\AdminUserEditType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
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

class UserAdminControllerTest extends TestCase
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
        $userRepository = $this->getUserRepositoryMock('findAll');

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $twig = $this->getTwigMock(true, 'admin/user/list.html.twig', [
            'users' => [],
        ]);

        $controller = new UserAdminController($twig, $entityManager);

        $this->assertInstanceOf(Response::class, $controller->listAction($userRepository));
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

        $formFactory = $this->getFormInterfaceMock(UserType::class, $form, $user);

        $passwordEncoder = $this->getUserPasswordEncoderMock($formIsSubmitted, $user);

        $entityManager = $this->getEntityManagerMock($formIsSubmitted, false, $formIsSubmitted, $user);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', 'L\'utilisateur a bien été ajouté.');

        $router = $this->getRouterMock($formIsSubmitted, 'admin_user_list');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'admin/user/create.html.twig', [
            'form' => null,
        ]);

        $controller = new UserAdminController($twig, $entityManager);

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

        $formFactory = $this->getFormInterfaceMock(AdminUserEditType::class, $form, $user);

        $passwordEncoder = $this->getUserPasswordEncoderMock($formIsSubmitted, $user);

        $entityManager = $this->getEntityManagerMock(false, false, $formIsSubmitted, $user);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', 'L\'utilisateur a bien été modifié');

        $router = $this->getRouterMock($formIsSubmitted, 'admin_user_list');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'admin/user/edit.html.twig', [
            'form' => null,
            'user' => $user,
        ]);

        $controller = new UserAdminController($twig, $entityManager);

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
     * @param $formType
     * @param $form
     * @param User $user
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|FormFactoryInterface
     */
    private function getFormInterfaceMock($formType, $form, User $user)
    {
        $formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $formFactoryMock->expects($this->once())
            ->method('create')
            ->with($formType, $user)
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
            ->with($user, $user->getPassword())
            ->willReturn('');

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

    /**
     * This helper method mocks UserRepository
     * and checks that a method is correctly called.
     *
     * @param string $method
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|UserRepository
     */
    private function getUserRepositoryMock(string $method)
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method($method);

        return $userRepository;
    }
}
