<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMINISTRATORIUS')) {
            return $this->redirectToRoute('app_administratorius_index');
        }

        if ($user && in_array('ROLE_VADYBININKAS', $user->getRoles())) {
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        if ($user && in_array('ROLE_USER', $user->getRoles())) {
            return $this->redirectToRoute('app_namai');
        }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('home/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
