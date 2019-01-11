<?php

namespace Tests\AppBundle\Controller;

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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testLogin()
    {
        $twigMock = $this->createMock(\Twig_Environment::class);
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
