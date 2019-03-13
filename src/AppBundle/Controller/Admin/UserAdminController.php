<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminUserEditType;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller used to manage the users in the backend.
 *
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class UserAdminController
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
     * UserAdminController constructor.
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
     * Lists all User entities.
     *
     * @Route("/users", methods={"GET"}, name="admin_user_list")
     *
     * @param UserRepository $userRepository
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function listAction(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return new Response(
            $this->twig->render('admin/user/list.html.twig', [
                'users' => $users,
            ])
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/users/create", methods={"GET", "POST"}, name="admin_user_create")
     *
     * @param FormFactoryInterface         $formFactory
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FlashBagInterface            $flashBag
     * @param RouterInterface              $router
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function createAction(FormFactoryInterface $formFactory, Request $request, UserPasswordEncoderInterface $passwordEncoder, FlashBagInterface $flashBag, RouterInterface $router): Response
    {
        $user = new User();

        $form = $formFactory->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $flashBag->add('success', "L'utilisateur a bien été ajouté.");

            return new RedirectResponse(
                $router->generate('admin_user_list')
            );
        }

        return new Response(
            $this->twig->render('admin/user/create.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/users/{id}/edit", methods={"GET", "POST"}, name="admin_user_edit")
     *
     * @param FormFactoryInterface         $formFactory
     * @param User                         $user
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FlashBagInterface            $flashBag
     * @param RouterInterface              $router
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return Response
     */
    public function editAction(FormFactoryInterface $formFactory, User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder, FlashBagInterface $flashBag, RouterInterface $router): Response
    {
        $form = $formFactory->create(AdminUserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->flush();

            $flashBag->add('success', "L'utilisateur a bien été modifié");

            return new RedirectResponse(
                $router->generate('admin_user_list')
            );
        }

        return new Response(
            $this->twig->render('admin/user/edit.html.twig', [
                'form' => $form->createView(), 'user' => $user,
            ])
        );
    }
}
