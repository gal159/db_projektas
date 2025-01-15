<?php

namespace App\Controller;

use App\Entity\Kaina;
use App\Entity\Paslauga;
use App\Repository\BendrijaRepository;
use App\Repository\PaslaugaRepository;
use App\Repository\KainaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/vadybininkas', name: 'app_vadybininkas')]
#[IsGranted('ROLE_VADYBININKAS')]
class VadybininkasController extends AbstractController
{
    #[Route('/kaina/{bendrijaId}', name: 'edit_kaina')]
    public function editKaina(
        int $bendrijaId,
        Request $request,
        EntityManagerInterface $em,
        PaslaugaRepository $paslaugaRepository,
        BendrijaRepository $bendrijaRepository,
        KainaRepository $kainaRepository
    ): Response {
        $bendrija = $bendrijaRepository->find($bendrijaId);
        $paslaugos = $paslaugaRepository->findAll();

        if (!$bendrija) {
            throw $this->createNotFoundException('Bendrija nerasta');
        }

        $kainosForm = [];
        foreach ($paslaugos as $paslauga) {
            $kaina = $kainaRepository->findOneBy([
                'bendrija_id' => $bendrija->getId(),
                'paslauga_id' => $paslauga->getId(),
            ]) ?? new Kaina();

            $kainosForm[$paslauga->getId()] = $kaina;
        }

        if ($request->isMethod('POST')) {
            foreach ($paslaugos as $paslauga) {
                $kainaReiksme = $request->request->get('kaina_' . $paslauga->getId());
                $kainosForm[$paslauga->getId()]->setKaina((int)$kainaReiksme * 100); // Paverčiama į centus
                $kainosForm[$paslauga->getId()]->setBendrijaId($bendrija);
                $kainosForm[$paslauga->getId()]->setPaslaugaId($paslauga);
                $em->persist($kainosForm[$paslauga->getId()]);
            }

            $em->flush();

            $this->addFlash('success', 'Kainos buvo atnaujintos!');
            return $this->redirectToRoute('app_vadybininkas');
        }

        return $this->render('vadybininkas/twig', [
            'bendrija' => $bendrija,
            'paslaugos' => $paslaugos,
            'kainosForm' => $kainosForm,
        ]);
    }
}
