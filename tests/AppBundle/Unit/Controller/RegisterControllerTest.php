<?php

namespace Tests\AppBundle\Unit\Controller;

use AppBundle\Controller\RegisterController;
use AppBundle\Entity\User;
use AppBundle\Form\UserRegisterType;
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

class RegisterControllerTest extends TestCase
{
    /**
     * This test checks that the method registerAction() is correctly returned
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
    public function testRegister(bool $formIsSubmitted, string $className)
    {
        $user = new User();

        $request = $this->createMock(Request::class);

        $form = $this->getFormMock($formIsSubmitted, $request);

        $formFactory = $this->getFormInterfaceMock($form, $user);

        $passwordEncoder = $this->getUserPasswordEncoderMock($formIsSubmitted, $user);

        $entityManager = $this->getEntityManagerMock($formIsSubmitted, false, $formIsSubmitted, $user);

        $flashBag = $this->getFlashBagMock($formIsSubmitted, 'success', sprintf('Bienvenue %s , votre compte a bien été crée.', ''));

        $router = $this->getRouterMock($formIsSubmitted, 'homepage');

        $twig = $this->getTwigMock(!$formIsSubmitted, 'register/register.html.twig', [
            'form' => null,
        ]);

        $controller = new RegisterController($twig, $entityManager);

        $this->assertInstanceOf($className, $controller->registerAction($formFactory, $request, $passwordEncoder, $flashBag, $router));
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
            ->with(UserRegisterType::class, $user)
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
     * @param User $task
     *
     * @throws \ReflectionException
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface
     */
    private function getEntityManagerMock(bool $callPersist, bool $callRemove, bool $callFlush, User $task)
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
}
