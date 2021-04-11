<?php

namespace App\Service;


use App\Entity\User;
use App\Entity\Caissier;
use App\Entity\AdminAgent;
use App\Entity\AdminSystem;
use App\Entity\Utilisateur;
use App\Entity\GroupeCompetences;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class addUser
{
    private $manager,
            $serializer, 
            $validator,
            $repo, 
            $encoder,
            $profilRepository;
    public function __construct(UserPasswordEncoderInterface $encoder,UserRepository $repo,
        EntityManagerInterface $manager,SerializerInterface $serializer, ValidatorInterface $validator,ProfilRepository $profilRepository)
        {
            $this->serializer = $serializer;
            $this->UserRepository = $repo;
            $this->manager = $manager;
            $this->validator = $validator;
            $this->encoder = $encoder;
            $this->profilRepository = $profilRepository;
        }
        public function addUser($request){
            $user = $request->request->all();
           dd($user);
            $profil=$this->profilRepository->find($user['profil']);
          
          
            if($profil->getLibelle() === "AdminSystem"){ 
              //  dd('ok');
                $userTab = $this->serializer->denormalize($user, AdminSystem::class, "json");
            }
            if($profil->getLibelle() === "AdminAgence"){
                $userTab = $this->serializer->denormalize($user, AdminAgent::class, "json");
            }
            if($profil->getLibelle() === "Caissier"){
                $userTab =$this->serializer->denormalize($user, Caissier::class, "json");
                /*$userTab->setStatus($user['status']);
                $userTab->setCategorie($user['categorie']);*/
            }
            if($profil->getLibelle() === "Utilisateur") {
                $userTab = $this->serializer->denormalize($user, Utilisateur::class, "json");
            }

            $userTab->setProfil($profil);
            $userTab->setphone($user['phone']);
            $userTab->setAddress($user['address']);
            $userTab->setArchived(false);
            $password = $userTab->getPassword();
            $userTab->setPassword($this->encoder->encodePassword($userTab, $password));
             $avatar = $request->files->get("avatar");
             $avatar = fopen($avatar->getRealPath(),"rb");
             $userTab->setAvatar($avatar);
           
             $this->manager->persist($userTab);
             $this->manager->flush();
              fclose($avatar);
                return $userTab;
        }

}
