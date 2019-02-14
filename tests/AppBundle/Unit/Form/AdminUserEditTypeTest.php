<?php

namespace Tests\AppBundle\Unit\Form;

use AppBundle\Entity\User;
use AppBundle\Form\AdminUserEditType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminUserEditTypeTest extends TypeTestCase
{
    private $validator;

    /**
     * This helper method mocks the ValidatorExtension.
     *
     * @throws \ReflectionException
     *
     * @return array
     */
    protected function getExtensions()
    {
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->validator
            ->method('validate')
            ->will($this->returnValue(new ConstraintViolationList()));
        $this->validator
            ->method('getMetadataFor')
            ->will($this->returnValue(new ClassMetadata(Form::class)));

        return [
            new ValidatorExtension($this->validator),
        ];
    }

    /**
     * This test checks that the Form and FormView
     * are correctly generated.
     */
    public function testBuildForm()
    {
        $formData = [
            'username' => 'user',
            'email' => 'user@email.com',
            'roles' => ['ROLE_USER'],
        ];

        $object = new User();
        $form = $this->factory->create(AdminUserEditType::class, $object);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
