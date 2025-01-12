<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class NamaiController extends AbstractController
{
    #[Route('/namai', name: 'app_namai')]
    public function index(): Response
    {
        return $this->render('namai/index.html.twig', [
            'controller_name' => 'NamaiController',
        ]);
    }
}
