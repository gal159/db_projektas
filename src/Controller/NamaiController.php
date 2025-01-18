<?php

namespace App\Controller;

use App\Repository\BendrijaRepository;
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
    public function index(PaslaugaRepository $paslaugaRepository, KainaRepository $kainaRepository, NaudotojasRepository $naudotojasRepository, BendrijaRepository $bendrijaRepository): Response
    {
        /** @var \App\Entity\Naudotojas $user */
        $user = $this->getUser();
        if (in_array('ROLE_VADYBININKAS', $user->getRoles())) {
            $bendrijos = $bendrijaRepository->findBy(['vadybininkas' => $user->getId()]);

            if (!$bendrijos) {
                $this->addFlash('error', 'Jums nepriskirta jokia bendrija.');
                return $this->redirectToRoute('app_home');
            }

            return $this->render('vadybininkas/index.html.twig', [
                'bendrijos' => $bendrijos,
            ]);
        }
            $kaina = $kainaRepository->findBy(['bendrija' => $user->getBendrija()->getId()]);
            $data = [];
            $i = 0;
            foreach ($kaina as $kaina) {
                $data[$i]['kaina'] = $kaina->getKaina();
                $data[$i]['paslauga'] = $kaina->getPaslauga()->getVardas();
                $data[$i]['matas'] = $kaina->getPaslauga()->getMatas();
                $i++;
            }

        $paslaugos = $paslaugaRepository->findAll();
        return $this->render('namai/index.html.twig', [  // Pakeista į tinkamą failo kelią
            'paslaugos' => $data,
        ]);
    }

}
