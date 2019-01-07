<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    /**
     * This test checks that the Form and FormView
     * are correctly generated.
     */
    public function testBuildForm()
    {
        $formData = [
            'title' => 'title',
            'content' => 'Lorem ipsum dolor sit amet.',
        ];

        $object = new Task();
        $form = $this->factory->create(TaskType::class, $object);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
