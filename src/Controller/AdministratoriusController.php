<?php

namespace App\Controller;

use App\Entity\Bendrija;
use App\Entity\Naudotojas;
use App\Entity\Paslauga;
use App\Repository\BendrijaRepository;
use App\Repository\NaudotojasRepository;
use App\Repository\PaslaugaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administratorius')]
#[IsGranted('ROLE_ADMINISTRATORIUS')]  // Tik administratorius turi prieigą
class AdministratoriusController extends AbstractController
{
    #[Route('/', name: 'app_administratorius_index')]
    public function index(
        BendrijaRepository $bendrijaRepository,
        NaudotojasRepository $naudotojasRepository,
        PaslaugaRepository $paslaugaRepository
    ): Response {
        return $this->render('administratorius/index.html.twig', [
            'bendrijos' => $bendrijaRepository->findAll(),
            'naudotojai' => $naudotojasRepository->findAll(),
            'paslaugos' => $paslaugaRepository->findAll(),
        ]);
    }

    #[Route('/bendrija/prideti', name: 'app_administratorius_bendrija_prideti')]
    public function pridetiBendrija(Request $request, EntityManagerInterface $entityManager, NaudotojasRepository $naudotojasRepository): Response
    {
        $vadybininkai = $naudotojasRepository->findBy(['role' => 'ROLE_VADYBININKAS']);  // Paimame visus vadybininkus

        if ($request->isMethod('POST')) {
            $pavadinimas = $request->request->get('pavadinimas');
            $vadybininkasId = $request->request->get('vadybininkas');

            $vadybininkas = $naudotojasRepository->find($vadybininkasId);

            $bendrija = new Bendrija();
            $bendrija->setPavadinimas($pavadinimas);
            $bendrija->setVadybininkas($vadybininkas);  // Priskiriame vadybininką

            $entityManager->persist($bendrija);
            $entityManager->flush();

            $this->addFlash('success', 'Bendrija sėkmingai sukurta!');
            return $this->redirectToRoute('app_administratorius_index');
        }

        return $this->render('administratorius/bendrija_prideti.html.twig', [
            'vadybininkai' => $vadybininkai,
        ]);
    }


    #[Route('/bendrija/salinti/{id}', name: 'app_administratorius_bendrija_salinti')]
    public function salintiBendrija(int $id, BendrijaRepository $bendrijaRepository, EntityManagerInterface $entityManager): Response {
        $bendrija = $bendrijaRepository->find($id);
        if (!$bendrija) {
            $this->addFlash('error', 'Tokia bendrija nerasta.');
        } else {
            $entityManager->remove($bendrija);
            $entityManager->flush();
            $this->addFlash('success', 'Bendrija sėkmingai pašalinta!');
        }
        return $this->redirectToRoute('app_administratorius_index');
    }

    #[Route('/paslauga/prideti', name: 'app_administratorius_paslauga_prideti')]
    public function pridetiPaslauga(Request $request, EntityManagerInterface $entityManager): Response {
        if ($request->isMethod('POST')) {
            $vardas = $request->request->get('vardas');
            $matas = $request->request->get('matas');
            $paslauga = new Paslauga();
            $paslauga->setVardas($vardas);
            $paslauga->setMatas($matas);
            $entityManager->persist($paslauga);
            $entityManager->flush();
            $this->addFlash('success', 'Paslauga sėkmingai pridėta!');
            return $this->redirectToRoute('app_administratorius_index');
        }
        return $this->render('administratorius/paslauga_prideti.html.twig');
    }

    #[Route('/paslauga/salinti/{id}', name: 'app_administratorius_paslauga_salinti')]
    public function salintiPaslauga(int $id, PaslaugaRepository $paslaugaRepository, EntityManagerInterface $entityManager): Response {
        $paslauga = $paslaugaRepository->find($id);
        if (!$paslauga) {
            $this->addFlash('error', 'Tokia paslauga nerasta.');
        } else {
            $entityManager->remove($paslauga);
            $entityManager->flush();
            $this->addFlash('success', 'Paslauga sėkmingai pašalinta!');
        }
        return $this->redirectToRoute('app_administratorius_index');
    }

    #[Route('/naudotojas/prideti', name: 'app_administratorius_naudotojas_prideti')]
    public function pridetiNaudotoja(Request $request, EntityManagerInterface $entityManager): Response {
        if ($request->isMethod('POST')) {
            $vardas = $request->request->get('vardas');
            $pavarde = $request->request->get('pavarde');
            $role = $request->request->get('role');

            $naudotojas = new Naudotojas();
            $naudotojas->setUsername($vardas);  // Automatiškai nustatomas naudotojo vardas kaip "vardas"
            $naudotojas->setPassword($pavarde);  // Automatiškai nustatomas slaptažodis kaip "pavardė"
            $naudotojas->setRole($role);

            $entityManager->persist($naudotojas);
            $entityManager->flush();

            $this->addFlash('success', 'Naudotojas sėkmingai pridėtas!');
            return $this->redirectToRoute('app_administratorius_index');
        }

        return $this->render('administratorius/naudotojas_prideti.html.twig');
    }


    #[Route('/naudotojas/salinti/{id}', name: 'app_administratorius_naudotojas_salinti')]
    public function salintiNaudotoja(int $id, NaudotojasRepository $naudotojasRepository, EntityManagerInterface $entityManager): Response {
        $naudotojas = $naudotojasRepository->find($id);
        if (!$naudotojas) {
            $this->addFlash('error', 'Toks naudotojas nerastas.');
        } else {
            $entityManager->remove($naudotojas);
            $entityManager->flush();
            $this->addFlash('success', 'Naudotojas sėkmingai pašalintas!');
        }
        return $this->redirectToRoute('app_administratorius_index');
    }

    #[Route('/bendrija/{id}/priskirti-vadybininka', name: 'app_administratorius_priskirti_vadybininka')]
    public function priskirtiVadybininka(
        int $id,
        BendrijaRepository $bendrijaRepository,
        NaudotojasRepository $naudotojasRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $bendrija = $bendrijaRepository->find($id);

        if (!$bendrija) {
            $this->addFlash('error', 'Bendrija nerasta.');
            return $this->redirectToRoute('app_administratorius_index');
        }

        // Paimame visus vartotojus, kurie turi rolę "ROLE_VADYBININKAS"
        $vadybininkai = $naudotojasRepository->findBy(['role' => 'ROLE_VADYBININKAS']);

        if ($request->isMethod('POST')) {
            $vadybininkasId = $request->request->get('vadybininkas');
            $vadybininkas = $naudotojasRepository->find($vadybininkasId);

            if (!$vadybininkas) {
                $this->addFlash('error', 'Vadybininkas nerastas.');
            } else {
                $bendrija->setVadybininkas($vadybininkas);
                $entityManager->persist($bendrija);
                $entityManager->flush();
                $this->addFlash('success', 'Vadybininkas sėkmingai priskirtas!');
            }

            return $this->redirectToRoute('app_administratorius_index');
        }

        return $this->render('administratorius/priskirti_vadybininka.html.twig', [
            'bendrija' => $bendrija,
            'vadybininkai' => $vadybininkai,
        ]);
    }

}
