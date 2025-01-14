<?php

namespace App\Controller;

use App\Repository\KainaRepository;
use App\Repository\NaudotojasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PaslaugaRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;




class NamaiController extends AbstractController
{
    #[Route('/namai', name: 'app_namai')]
    #[IsGranted('ROLE_USER')]
    public function index(PaslaugaRepository $paslaugaRepository, KainaRepository $kainaRepository, NaudotojasRepository $naudotojasRepository): Response
    {
        $user = $this->getUser();
        var_dump($user);
        $userdata = $naudotojasRepository->findBy(['id' => '1']);
        var_dump($userdata);
        $kaina = $kainaRepository->findBy(['bedrija' => $user->getUserIdentifier()]);
        $paslaugos = $paslaugaRepository->findAll();
        return $this->render('namai/index.html.twig', [  // Pakeista į tinkamą failo kelią
            'paslaugos' => $paslaugos,
            'kaina' => $kaina,
        ]);
    }

}
