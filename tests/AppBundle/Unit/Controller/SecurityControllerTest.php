<?php

namespace Tests\AppBundle\Unit\Controller;

use AppBundle\Controller\SecurityController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityControllerTest extends TestCase
{
    /**
     * This test checks that the method loginAction() is correctly returned
     * and checks that the methods render(), getLastUsername(), getLastAuthenticationError()
     * are correctly called.
     *
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testLogin()
    {
        $twigMock = $this->createMock(\Twig\Environment::class);
        $twigMock->expects($this->once())
            ->method('render')
            ->with('security/login.html.twig');

        $controller = new SecurityController($twigMock);

        $authenticationMock = $this->createMock(AuthenticationUtils::class);
        $authenticationMock->expects($this->once())
            ->method('getLastUsername');
        $authenticationMock->expects($this->once())
            ->method('getLastAuthenticationError');

        $this->assertInstanceOf(Response::class, $controller->loginAction($authenticationMock));
    }
}
