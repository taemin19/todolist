<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class TaskController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TaskController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/tasks", name="task_list")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function listAction()
    {
        return new Response(
            $this->twig->render('task/list.html.twig', [
                'tasks' => $this->entityManager->getRepository('AppBundle:Task')->findBy(['isDone' => false]),
            ])
        );
    }

    /**
     * @Route("/tasks/done", name="task_done")
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return Response
     */
    public function listDoneAction()
    {
        return new Response(
            $this->twig->render('task/list_done.html.twig', [
                'tasks' => $this->entityManager->getRepository('AppBundle:Task')->findBy(['isDone' => true]),
            ])
        );
    }

    /**
     * @Route("/tasks/create", name="task_create")
     *
     * @param FormFactoryInterface $formFactory
     * @param Request              $request
     * @param FlashBagInterface    $flashBag
     * @param RouterInterface      $router
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return RedirectResponse|Response
     */
    public function createAction(FormFactoryInterface $formFactory, Request $request, FlashBagInterface $flashBag, RouterInterface $router)
    {
        $task = new Task();

        $form = $formFactory->create(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $flashBag->add('success', 'La tâche a bien été ajoutée.');

            return new RedirectResponse(
                $router->generate('task_list')
            );
        }

        return new Response(
            $this->twig->render('task/create.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     *
     * @param FormFactoryInterface $formFactory
     * @param Request              $request
     * @param FlashBagInterface    $flashBag
     * @param Task                 $task
     * @param RouterInterface      $router
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return RedirectResponse|Response
     */
    public function editAction(FormFactoryInterface $formFactory, Task $task, Request $request, FlashBagInterface $flashBag, RouterInterface $router)
    {
        $form = $formFactory->create(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $flashBag->add('success', 'La tâche a bien été modifiée.');

            return new RedirectResponse(
                $router->generate('task_list')
            );
        }

        return new Response(
            $this->twig->render('task/edit.html.twig', [
                'form' => $form->createView(),
                'task' => $task,
            ])
        );
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     *
     * @param Task              $task
     * @param FlashBagInterface $flashBag
     * @param RouterInterface   $router
     *
     * @return RedirectResponse
     */
    public function toggleTaskAction(Task $task, FlashBagInterface $flashBag, RouterInterface $router)
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        if ($task->isDone()) {
            $flashBag->add('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

            return new RedirectResponse(
                $router->generate('task_list')
            );
        }

        $flashBag->add('success', sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle()));

        return new RedirectResponse(
                $router->generate('task_done')
            );
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     *
     * @param Task              $task
     * @param FlashBagInterface $flashBag
     * @param RouterInterface   $router
     *
     * @return RedirectResponse
     */
    public function deleteTaskAction(Task $task, FlashBagInterface $flashBag, RouterInterface $router)
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $flashBag->add('success', 'La tâche a bien été supprimée.');

        return new RedirectResponse(
            $router->generate('task_list')
        );
    }
}
