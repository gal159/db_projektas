<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdministratoriusController extends AbstractController
{
    #[Route('/administratorius', name: 'app_administratorius')]
    public function index(): Response
    {
        return $this->render('administratorius/index.html.twig', [
            'controller_name' => 'AdministratoriusController',
        ]);
    }
}
