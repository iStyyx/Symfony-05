<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('login/index.html.twig', [
        'last_username' => $lastUsername,
        'error'         => $error,
        ]);
    }

    /**
     * @Route("/my-profile", name="profile")
     */
    public function showProfile(): Response
    {
        if (empty($this->getUser())) {
            return $this->redirectToRoute('login');
        } else {
            dump($this->getUser()->getComments());
            return $this->render('my-profile.html.twig', [
                'comments' => $this->getUser()->getComments()
            ]);
        }
    }
}
