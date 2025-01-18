<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response{
        return $security->logout();
    }
    #[Route('/home', name: 'app_home')]
    public function home(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();
            return $this->render('home/index.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        if (in_array('ROLE_ADMINISTRATORIUS', $user->getRoles())) {
            return $this->redirectToRoute('app_administratorius_index');
        }

        if (in_array('ROLE_VADYBININKAS', $user->getRoles())) {
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        if (in_array('ROLE_USER', $user->getRoles())) {
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
