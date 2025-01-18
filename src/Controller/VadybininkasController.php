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
use App\Repository\NaudotojasRepository;

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

        if (!$bendrija || $bendrija->getVadybininkas()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Jūs neturite prieigos prie šios bendrijos.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        $paslaugos = $paslaugaRepository->findAll();

        // Gauti paslaugas, kurios jau priskirtos šiai bendrijai
        $pazymetosPaslaugos = [];
        foreach ($kainaRepository->findBy(['bendrija' => $bendrija]) as $kaina) {
            $pazymetosPaslaugos[] = $kaina->getPaslauga()->getId();
        }

        // Gauname kainas ir jas konvertuojame į asociatyvų masyvą
        $kainos = [];
        foreach ($kainaRepository->findBy(['bendrija' => $bendrija]) as $kaina) {
            $kainos[$kaina->getPaslauga()->getId()] = $kaina->getKaina();
        }

        if ($request->isMethod('POST')) {
            foreach ($paslaugos as $paslauga) {
                $kainaReiksme = $request->request->get('kaina_' . $paslauga->getId());

                if ($kainaReiksme !== null) {
                    $kaina = $kainaRepository->findOneBy([
                        'bendrija' => $bendrija,
                        'paslauga' => $paslauga,
                    ]);

                    if (!$kaina) {
                        $kaina = new \App\Entity\Kaina();
                        $kaina->setBendrija($bendrija);
                        $kaina->setPaslauga($paslauga);
                    }

                    $kaina->setKaina((int) $kainaReiksme );
                    $entityManager->persist($kaina);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Kainos sėkmingai atnaujintos!');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        return $this->render('vadybininkas/edit.html.twig', [
            'bendrija' => $bendrija,
            'paslaugos' => $paslaugos,
            'kainos' => $kainos,
            'pazymetosPaslaugos' => $pazymetosPaslaugos,  // Pridedame į Twig šabloną
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
        $kainos = $kainaRepository->findBy(['bendrija' => $bendrija]);
        foreach ($kainos as $kaina) {
            $kainosAssoc[$kaina->getPaslauga()->getId()] = $kaina->getKaina();
        }
        return $this->render('vadybininkas/show.html.twig', [
            'bendrija' => $bendrija,
            'paslaugos' => $paslaugos,
            'kainos' => $kainosAssoc,  // Pakeista į asociatyvų masyvą pagal paslaugos ID
        ]);

    }
    #[Route('/bendrija/{bendrijaId}/priskirti-gyventojus', name: 'app_vadybininkas_priskirti_gyventojus')]
    public function priskirtiGyventojus(
        int $bendrijaId,
        BendrijaRepository $bendrijaRepository,
        NaudotojasRepository $naudotojasRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $user = $this->getUser();
        $bendrija = $bendrijaRepository->find($bendrijaId);

        if (!$bendrija || $bendrija->getVadybininkas()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Jūs neturite prieigos prie šios bendrijos.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        $gyventojai = $naudotojasRepository->findBy(['role' => 'ROLE_USER']);

        if ($request->isMethod('POST')) {
            $pasirinktiGyventojai = $request->request->all('gyventojai');  // Paima tik pažymėtus gyventojus

            foreach ($gyventojai as $gyventojas) {
                if (in_array($gyventojas->getId(), $pasirinktiGyventojai)) {
                    // Priskiriame gyventoją tik jei jis buvo pažymėtas
                    $gyventojas->setBendrija($bendrija);
                    $entityManager->persist($gyventojas);
                }
            }
            $entityManager->flush();

            $this->addFlash('success', 'Gyventojai sėkmingai priskirti.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        return $this->render('vadybininkas/priskirti_gyventojus.html.twig', [
            'bendrija' => $bendrija,
            'gyventojai' => $gyventojai,
        ]);
    }


    #[Route('/bendrija/{bendrijaId}/priskirti-paslaugas', name: 'app_vadybininkas_priskirti_paslaugas')]
    public function priskirtiPaslaugas(
        int $bendrijaId,
        BendrijaRepository $bendrijaRepository,
        PaslaugaRepository $paslaugaRepository,
        KainaRepository $kainaRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $user = $this->getUser();
        $bendrija = $bendrijaRepository->find($bendrijaId);

        if (!$bendrija || $bendrija->getVadybininkas()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Jūs neturite prieigos prie šios bendrijos.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        $paslaugos = $paslaugaRepository->findAll();

        // Surandame jau pažymėtas paslaugas (jos turi kainą)
        $pazymetosPaslaugos = [];
        $kainos = $kainaRepository->findBy(['bendrija' => $bendrija]);
        foreach ($kainos as $kaina) {
            $pazymetosPaslaugos[] = $kaina->getPaslauga()->getId();
        }

        if ($request->isMethod('POST')) {
            $pasirinktosPaslaugos = $request->request->all('paslaugos');

            // Pereiname per visas paslaugas ir atnaujiname jų priskyrimą
            foreach ($paslaugos as $paslauga) {
                $kaina = $kainaRepository->findOneBy([
                    'bendrija' => $bendrija,
                    'paslauga' => $paslauga,
                ]);

                if (in_array($paslauga->getId(), $pasirinktosPaslaugos)) {
                    // Jei paslauga pasirinkta, bet dar neturi kainos – sukuriame naują įrašą
                    if (!$kaina) {
                        $kaina = new \App\Entity\Kaina();
                        $kaina->setBendrija($bendrija);
                        $kaina->setPaslauga($paslauga);
                        $kaina->setKaina(0);
                        $entityManager->persist($kaina);
                    }
                } else {
                    // Jei paslauga anksčiau buvo pasirinkta, bet dabar atžymėta – pašaliname
                    if ($kaina) {
                        $entityManager->remove($kaina);
                    }
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Paslaugos sėkmingai priskirtos.');
            return $this->redirectToRoute('app_vadybininkas_index');
        }

        return $this->render('vadybininkas/priskirti_paslaugas.html.twig', [
            'bendrija' => $bendrija,
            'paslaugos' => $paslaugos,
            'pazymetosPaslaugos' => $pazymetosPaslaugos, // Siunčiame į Twig šabloną
        ]);
    }

}
