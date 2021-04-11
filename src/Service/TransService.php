<?php


namespace App\Service;

use App\Entity\User;
use App\Entity\Profil;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GrilleTarifRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class TransService
{
    private $serializer;
    private $validator;
    private $encoder;
    private $manager;
    private $grilleTarifRepo;
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, 
    UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, GrilleTarifRepository $grilleTarifRepo,
    ProfilRepository $repoProfil, UserRepository $repoUser){
        $this->serializer=$serializer;
        $this->validator=$validator;
        $this->encoder=$encoder;
        $this->manager=$manager;
        $this->repoProfil=$repoProfil;
        $this->repoUser=$repoUser;
        $this->grilleTarifRepo=$grilleTarifRepo;
    }

    public function CalculFrais($solde){
        $frais = $this->grilleTarifRepo->findAll();
        //dd($tarif);
        foreach ($frais as $value) {
            if ($solde >= $value->getMin() && $solde < $value->getMax()) {
                $frais= $value->getTarif();
                //dd($frais);
                return $frais;
            }
        }
        if ($solde > 2000000) {
            $frais= ($solde * 2) / 100;
            //dd($frais);
            return $frais;
        }
    }

    public function CalculPart($frais){

        $parts = [];
        $parts['PartEtat'] = ($frais * 40) / 100;
        $parts['PartSystem'] = ($frais * 30) / 100;
        $parts['PartDepot'] = ($frais * 10) / 100;
        $parts['PartRetrait'] = ($frais * 20) / 100;

        return $parts;

    }
    
    public function Operation(Request $request){

        $post = $request->getContent();
        $data = $this->serializer->decode($post, "json");
        $frais = $this->CalculFrais($data["montant"]);
        $partEtat = $this->CalculPart($frais);
    }

     function Code($length = 9) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randString = '';
        for ($i = 0; $i < $length; $i++) {
            $randString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randString;
    }

    public function validationCni($cni){
        if (ctype_digit($cni)){
            if (strlen($cni)==13){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    public function validationPhone($telephone){
        if (ctype_digit($telephone)){
            if (strlen($telephone)==9){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    
}