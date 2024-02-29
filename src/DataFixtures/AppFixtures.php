<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher){

    }

    public function load(ObjectManager $manager): void
    {
        //CREATION DES ETATS PAR DEFAULT
        $cree = new Etat();
        $cree->setLibelle('Crée');
        $manager->persist($cree);

        $ouverte = new Etat();
        $ouverte->setLibelle('Ouverte');
        $manager->persist($ouverte);

        $cloturee = new Etat();
        $cloturee->setLibelle('Clôturée');
        $manager->persist($cloturee);

        $enCours = new Etat();
        $enCours->setLibelle('Activité en cours');
        $manager->persist($enCours);

        $passee = new Etat();
        $passee->setLibelle('Passée');
        $manager->persist($passee);

        $annulee = new Etat();
        $annulee->setLibelle('Annulée');
        $manager->persist($annulee);

        $archivee = new Etat();
        $archivee->setLibelle('Archivée');
        $manager->persist($archivee);

        $manager->flush();

        //CREATION DES SITES PAR DEFAULT
        //SAINT HERBLAIN, CHARTRES DE BRETAGNE, LA ROCHE SUR YON.
        $stHerblain = new Site();
        $stHerblain->setNom('Saint Herblain');
        $manager->persist($stHerblain);

        $chartresDeBretagne = new Site();
        $chartresDeBretagne->setNom('Chartres de Bretagne');
        $manager->persist($chartresDeBretagne);

        $laRocheSurYon = new Site();
        $laRocheSurYon->setNom('La Roche Sur Yon');
        $manager->persist($laRocheSurYon);

        $manager->flush();

        //CREATION D'UN COMPTE ADMIN PAR DEFAULT
        $admin = new Participant();
        $admin->setNom('Admin');
        $admin->setSite($stHerblain);
        $admin->setActif(true);
        $admin->setEmail('admin@admin.com');
        $admin->setPrenom('Admin');
        $admin->setPassword(
            $this->userPasswordHasher->hashPassword(
                $admin,
                'Pa$$w0rd'
            )
        );
        $admin->setRoles(["ROLE_ADMIN"]
        );
        $manager->persist($admin);

        $manager->flush();
    }
}
