<?php

namespace Tests\AppBundle\Unit\Controller;

use AppBundle\Controller\DefaultController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends TestCase
{
    /**
     * This test checks that the method indexAction() is correctly returned
     * and checks that the template is correctly called.
     *
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testIndex()
    {
        $twigMock = $this->createMock(\Twig_Environment::class);
        $twigMock->expects($this->once())
            ->method('render')
            ->with('default/index.html.twig');

        $controller = new DefaultController($twigMock);

        $this->assertInstanceOf(Response::class, $controller->indexAction());
    }
}
