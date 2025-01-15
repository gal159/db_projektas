<?php

namespace App\DataFixtures;

use App\Entity\Bendrija;
use App\Entity\Kaina;
use App\Entity\Naudotojas;
use App\Entity\Paslauga;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new Naudotojas())
            ->setUsername("Dziugas")
            ->setPassword("secret")
            ->setRole("ROLE_ADMINISTRATORIUS");
        $manager->persist($user);

        $vadybininkas = (new Naudotojas())
            ->setUsername("Augustinas")
            ->setPassword("secret")
            ->setRole("ROLE_VADYBININKAS");
        $manager->persist($vadybininkas);
        $manager->flush();

        $this->loadPaslauga($manager);
        $this->loadBendrija($manager);
        $this->naudotojas($manager);
        $this->loadKaina($manager);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function naudotojas(ObjectManager $manager): void
    {
        $bendrijos = $manager->getRepository(Bendrija::class)->findAll();
        for ($i = 0; $i < 100; $i++) {
            $z = rand(0,9);

            $product = (new Naudotojas())
                ->setUsername("tomas$i")
                ->setPassword("tomas$i")
                ->setRole("ROLE_USER")
                ->setBendrija($bendrijos[$z]);
            $manager->persist($product);
        }
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function loadPaslauga(ObjectManager $manager): void
    {
        $product = (new Paslauga())
            ->setVardas("elektra")
            ->setMatas("€/kWh");
        $manager->persist($product);

        $product = (new Paslauga())
            ->setVardas("vanduo")
            ->setMatas("€/m³");
        $manager->persist($product);

        $product = (new Paslauga())
            ->setVardas("dujos")
            ->setMatas("€/m³");
        $manager->persist($product);

        $product = (new Paslauga())
            ->setVardas("sildymas")
            ->setMatas("ct/kWh");
        $manager->persist($product);

        $product = (new Paslauga())
            ->setVardas("internetas")
            ->setMatas("€/mėn");
        $manager->persist($product);

        $manager->flush();
    }

    public function loadKaina(ObjectManager $manager): void
    {
        $bendrijos = $manager->getRepository(Bendrija::class)->findAll();
        $elektra = $manager->getRepository(Paslauga::class)->findOneBy(["vardas" => "elektra"]);
        $vanduo = $manager->getRepository(Paslauga::class)->findOneBy(["vardas" => "vanduo"]);
        $dujos = $manager->getRepository(Paslauga::class)->findOneBy(["vardas" => "dujos"]);
        $sildymas = $manager->getRepository(Paslauga::class)->findOneBy(["vardas" => "sildymas"]);
        $internetas = $manager->getRepository(Paslauga::class)->findOneBy(["vardas" => "internetas"]);
        foreach ($bendrijos as $bendrija) {
            $kaina = (new Kaina())
                ->setBendrija($bendrija)
                ->setKaina(18)
                ->setPaslauga($elektra);
            $manager->persist($kaina);

            $kaina = (new Kaina())
                ->setBendrija($bendrija)
                ->setKaina(352)
                ->setPaslauga($vanduo);
            $manager->persist($kaina);

            $kaina = (new Kaina())
                ->setBendrija($bendrija)
                ->setKaina(65)
                ->setPaslauga($dujos);
            $manager->persist($kaina);

            $kaina = (new Kaina())
                ->setBendrija($bendrija)
                ->setKaina(981)
                ->setPaslauga($sildymas);
            $manager->persist($kaina);

            $kaina = (new Kaina())
                ->setBendrija($bendrija)
                ->setKaina(1100)
                ->setPaslauga($internetas);
            $manager->persist($kaina);
        }

        $manager->flush();
    }

    Public function loadBendrija(ObjectManager $manager): void
    {
        $vadybininkai = $manager->getRepository(Naudotojas::class)->findBy(["role" => "ROLE_VADYBININKAS"]);
        for ($i = 1; $i < 11; $i++) {
            $bendrija = (new Bendrija())
                ->setPavadinimas("Namas Nr. $i")
                ->setVadybininkas($vadybininkai[rand(0, count($vadybininkai) - 1)]);
            $manager->persist($bendrija);
        }
        $manager->flush();
    }
}
