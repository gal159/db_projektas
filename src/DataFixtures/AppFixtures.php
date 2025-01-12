<?php

namespace App\DataFixtures;

use App\Entity\Bendrija;
use App\Entity\Naudotojas;
use App\Entity\Paslauga;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadPaslauga($manager);
        $this->loadBendrija($manager);
        $this->naudotojas($manager);
        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function naudotojas(ObjectManager $manager): void
    {

        for ($i = 0; $i < 100; $i++) {
            $z = rand(1,10);
            $bendrija = $manager->getRepository(Bendrija::class)->findOneBy(['pavadinimas' => "Namas Nr. $z"]);

            $product = (new Naudotojas())
                ->setUsername("tomas$i")
                ->setPassword("tomas$i")
                ->setRole("gyventojas")
                ->setBendrija($bendrija);
            $manager->persist($product);
        }


        $user = (new Naudotojas())
            ->setUsername("Dziugas")
            ->setPassword("secret")
            ->setRole("administratorius");
        $manager->persist($user);
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    public function loadPaslauga(ObjectManager $manager): void
    {
        $vadybininkas = (new Naudotojas())
            ->setUsername("Augustinas")
            ->setPassword("secret")
            ->setRole("vadybininkas");
        $manager->persist($vadybininkas);

        for ($i = 0; $i < 10; $i++) {
            $product = (new Paslauga())
                ->setVardas("tomas$i")
                ->setKaina($i)
                ->setVadybininkas($vadybininkas);
            $manager->persist($product);
        }
        $manager->flush();
    }

    Public function loadBendrija(ObjectManager $manager): void
    {

        for ($i = 1; $i < 11; $i++) {
            $bendrija = (new Bendrija())
                ->setPavadinimas("Namas Nr. $i");
            $manager->persist($bendrija);
        }
        $manager->flush();
    }
}
