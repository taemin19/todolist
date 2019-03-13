<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Controller used to manage task contents.
 *
 * @Route("/tasks")
 * @IsGranted("ROLE_USER")
 */
class TaskController
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TaskController constructor.
     *
     * @param \Twig\Environment      $twig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(\Twig\Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="task_list")
     *
     * @param TokenStorageInterface $tokenStorage
     * @param TaskRepository        $taskRepository
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function listAction(TokenStorageInterface $tokenStorage, TaskRepository $taskRepository): Response
    {
        $user = $tokenStorage->getToken()->getUser();

        $tasks = $taskRepository->findAllByUser($user);

        return new Response(
            $this->twig->render('task/list.html.twig', [
                'tasks' => $tasks,
            ])
        );
    }

    /**
     * @Route("/done", name="task_done")
     *
     * @param TokenStorageInterface $tokenStorage
     * @param TaskRepository        $taskRepository
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function listDoneAction(TokenStorageInterface $tokenStorage, TaskRepository $taskRepository): Response
    {
        $user = $tokenStorage->getToken()->getUser();

        $tasks = $taskRepository->findAllIsDoneByUser($user);

        return new Response(
            $this->twig->render('task/list_done.html.twig', [
                'tasks' => $tasks,
            ])
        );
    }

    /**
     * @Route("/create", name="task_create")
     *
     * @param TokenStorageInterface $tokenStorage
     * @param FormFactoryInterface  $formFactory
     * @param Request               $request
     * @param FlashBagInterface     $flashBag
     * @param RouterInterface       $router
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function createAction(TokenStorageInterface $tokenStorage, FormFactoryInterface $formFactory, Request $request, FlashBagInterface $flashBag, RouterInterface $router): Response
    {
        $user = $tokenStorage->getToken()->getUser();

        $task = new Task();
        $task->setUser($user);

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
     * @Route("/{id}/edit", name="task_edit")
     * @IsGranted("edit", subject="task")
     *
     * @param FormFactoryInterface $formFactory
     * @param Task                 $task
     * @param Request              $request
     * @param FlashBagInterface    $flashBag
     * @param RouterInterface      $router
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function editAction(FormFactoryInterface $formFactory, Task $task, Request $request, FlashBagInterface $flashBag, RouterInterface $router): Response
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
     * @Route("/{id}/toggle", name="task_toggle")
     *
     * @param Task              $task
     * @param FlashBagInterface $flashBag
     * @param RouterInterface   $router
     *
     * @return Response
     */
    public function toggleTaskAction(Task $task, FlashBagInterface $flashBag, RouterInterface $router): Response
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
     * @Route("/{id}/delete", name="task_delete")
     * @IsGranted("delete", subject="task")
     *
     * @param Task              $task
     * @param FlashBagInterface $flashBag
     * @param RouterInterface   $router
     *
     * @return Response
     */
    public function deleteTaskAction(Task $task, FlashBagInterface $flashBag, RouterInterface $router): Response
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $flashBag->add('success', 'La tâche a bien été supprimée.');

        return new RedirectResponse(
            $router->generate('task_list')
        );
    }
}
