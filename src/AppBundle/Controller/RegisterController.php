<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Controller used to register a user account.
 */
class RegisterController
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
     * RegisterController constructor.
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
     * @Route("/register", methods={"GET", "POST"}, name="user_register")
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
    public function registerAction(FormFactoryInterface $formFactory, Request $request, UserPasswordEncoderInterface $passwordEncoder, FlashBagInterface $flashBag, RouterInterface $router): Response
    {
        $user = new User();

        $form = $formFactory->create(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $flashBag->add('success', sprintf('Bienvenue %s , votre compte a bien été crée.', $user->getUsername()));

            return new RedirectResponse(
                $router->generate('homepage')
            );
        }

        return new Response(
            $this->twig->render('register/register.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
