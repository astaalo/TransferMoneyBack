<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');
        $profils =["AdminSystem","AdminAgent" ,"Caissier" ,"Utilisateur"];
        foreach ($profils as $key => $libelle) {
            $profil = new Profil();
            $profil->setLibelle($libelle);
            $manager->persist($profil);
            $manager->flush();
            $user = new User();
            $user->setProfil($profil);
            $user->setFirstname($faker->firstname);
            $user->setLastname($faker->name);
            $user->setPhone($faker->phoneNumber);
            $user->setCni(21454);
            $user->setUsername($faker->userName);
            $user->setAddress($faker->address);
            $user->setArchived(0);

            //Génération des Users
            $password = $this->encoder->encodePassword($user, 'pass_1234');
            $user->setPassword($password);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
