<?php

namespace App\Controller;

use App\Repository\BendrijaRepository;
use App\Repository\KainaRepository;
use App\Repository\PaslaugaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/vadybininkas')]
#[IsGranted('ROLE_VADYBININKAS')]
class VadybininkasController extends AbstractController
{
    #[Route('/', name: 'app_vadybininkas_index')]
    public function index(BendrijaRepository $bendrijaRepository): Response
    {
        $user = $this->getUser();

        // Gauti visas bendrijas, kuriose vadybininkas priskirtas
        $bendrijos = $bendrijaRepository->findBy(['vadybininkas' => $user->getId()]);

        if (!$bendrijos) {
            $this->addFlash('error', 'Jums nepriskirta jokia bendrija.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('vadybininkas/index.html.twig', [
            'bendrijos' => $bendrijos,
        ]);
    }

    #[Route('/kaina/{bendrijaId}', name: 'app_vadybininkas_edit_kaina')]
    public function editKaina(
        int $bendrijaId,
        BendrijaRepository $bendrijaRepository,
        PaslaugaRepository $paslaugaRepository,
        KainaRepository $kainaRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $bendrija = $bendrijaRepository->find($bendrijaId);

        // Tikriname, ar vadybininkas turi prieigą prie bendrijos
        if (!$bendrija || $bendrija->getVadybininkas()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Jūs neturite prieigos prie šios bendrijos.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        $paslaugos = $paslaugaRepository->findAll();
        $kainos = $kainaRepository->findBy(['bendrija' => $bendrija]);

        // Tvarkyti POST duomenis, jei formoje paspaudė "Išsaugoti"
        if ($request->isMethod('POST')) {
            foreach ($paslaugos as $paslauga) {
                $kainaReiksme = $request->request->get('kaina_' . $paslauga->getId());

                // Ieškome kainos įrašo pagal bendriją ir paslaugą
                $kaina = $kainaRepository->findOneBy([
                    'bendrija' => $bendrija,
                    'paslauga' => $paslauga,
                ]);

                // Jei tokios kainos nėra – sukuriame naują
                if (!$kaina) {
                    $kaina = new \App\Entity\Kaina();
                    $kaina->setBendrija($bendrija);
                    $kaina->setPaslauga($paslauga);
                }

                // Atnaujiname kainą
                $kaina->setKaina((int) $kainaReiksme * 100);  // Išsaugoma kaina centais
                $entityManager->persist($kaina);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Kainos sėkmingai atnaujintos!');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        return $this->render('vadybininkas/edit.html.twig', [
            'bendrija' => $bendrija,
            'paslaugos' => $paslaugos,
            'kainos' => $kainos,
        ]);
    }
    #[Route('/kaina/perziura/{bendrijaId}', name: 'app_vadybininkas_show_kaina')]
    public function showKainos(
        int $bendrijaId,
        BendrijaRepository $bendrijaRepository,
        PaslaugaRepository $paslaugaRepository,
        KainaRepository $kainaRepository
    ): Response {
        $user = $this->getUser();
        $bendrija = $bendrijaRepository->find($bendrijaId);

        // Tikriname, ar vadybininkas turi prieigą prie bendrijos
        if (!$bendrija || $bendrija->getVadybininkas()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Jūs neturite prieigos prie šios bendrijos.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        $paslaugos = $paslaugaRepository->findAll();
        $kainosAssoc = [];
        foreach ($kainaRepository->findBy(['bendrija' => $bendrija]) as $kaina) {
            $kainosAssoc[$kaina->getPaslauga()->getId()] = $kaina->getKaina();
        }
        return $this->render('vadybininkas/show.html.twig', [
            'bendrija' => $bendrija,
            'paslaugos' => $paslaugos,
            'kainos' => $kainosAssoc,  // Pakeista į asociatyvų masyvą pagal paslaugos ID
        ]);

    }

}
