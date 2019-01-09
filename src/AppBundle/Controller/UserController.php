<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController
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
     * UserController constructor.
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
     * @Route("/users", name="user_list")
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
            $this->twig->render('user/list.html.twig', [
                'users' => $this->entityManager->getRepository('AppBundle:User')->findAll(),
            ])
        );
    }

    /**
     * @Route("/users/create", name="user_create")
     *
     * @param FormFactoryInterface         $formFactory
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FlashBagInterface            $flashBag
     * @param RouterInterface              $router
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return RedirectResponse|Response
     */
    public function createAction(FormFactoryInterface $formFactory, Request $request, UserPasswordEncoderInterface $passwordEncoder, FlashBagInterface $flashBag, RouterInterface $router)
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
                $router->generate('user_list')
            );
        }

        return new Response(
            $this->twig->render('user/create.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     *
     * @param FormFactoryInterface         $formFactory
     * @param User                         $user
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FlashBagInterface            $flashBag
     * @param RouterInterface              $router
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return RedirectResponse|Response
     */
    public function editAction(FormFactoryInterface $formFactory, User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder, FlashBagInterface $flashBag, RouterInterface $router)
    {
        $form = $formFactory->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->flush();

            $flashBag->add('success', "L'utilisateur a bien été modifié");

            return new RedirectResponse(
                $router->generate('user_list')
            );
        }

        return new Response(
            $this->twig->render('user/edit.html.twig', [
                'form' => $form->createView(), 'user' => $user,
            ])
        );
    }
}
