<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Service\TransService;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use App\Repository\CommissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TransactionController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager,
    UserRepository $userRepository, TransService $transService)
    {
        $this->userRepository=$userRepository;
        $this->transService=$transService;
    }
    /**
     * @Route("api/admin/transactions/depot", name="add_frais",methods={"POST"})
     */
    public function addFrais(Request $request, SerializerInterface $serializer)
    {
        $transaction = new Transaction();
        $clientDepot= new Client();
        $clientRetrait= new Client();

        $transactionJson= $request->getContent();
        $transactionTab= $serializer->decode($transactionJson, 'json');
       
        $user=$this->getUser();
        //dd($user);
        $user= $this->userRepository->find($user);
        //dd($user);
        $agenceDepot =$user->getAgence();
        //dd($agenceDepot);
        $compteAgenceDepot =$agenceDepot->getCompte();
        //dd($compteAgenceDepot);
        $montantDepot =$transactionTab['montant'];
        $soldeCompteAgenceDepot = $compteAgenceDepot->getSolde();
        //dd($soldeCompteAgenceDepot);
        if ($soldeCompteAgenceDepot<5000){
            return $this->json('votre solde est inferieur a 5000',Response::HTTP_NOT_FOUND);
        }
        if ($montantDepot<$soldeCompteAgenceDepot){
            $compteAgenceDepot->setSolde($soldeCompteAgenceDepot-$montantDepot);
            $entityManager=$this->getDoctrine()->getManager();
            $transaction->setUserDepot($user);

            $transaction->setCompte($compteAgenceDepot);
            $transaction->setMontant($montantDepot);

            $dateDepot=new \DateTime();
            //dd($dateDepot);
            $transaction->setDateDepot($dateDepot);
            $codeTrans= $this->transService->code();
            //dd($codeTrans);
            $transaction->setCodeTrans(wordwrap($codeTrans, 3, "-",  true));
            $frais= $this->transService->CalculFrais($montantDepot);
            //dd($frais);
            $transaction->setFrais($frais);
            $parts=$this->transService->CalculPart($frais);
            //dd($parts);
            $transaction->setFraisDepot($frais);
            $transaction->setFraisEtat($parts['PartEtat']);
            $transaction->setFraisSystem($parts['PartSystem']);
            $transaction->setFraisDepot($parts['PartDepot']);
            $transaction->setFraisRetrait($parts['PartRetrait']);
            $solde=($transaction->getCompte()->getSolde() + $parts['PartDepot']) - $transaction->getMontant();
            //dd($solde);
            $transaction->getCompte()->setSolde($solde);

            
            $clientDepot->setNom($transactionTab['clientDepot']['nomEmetteur']);
            $clientDepot->setPrenom($transactionTab['clientDepot']['prenomEmetteur']);
            //dd($clientDepot);
            if ($this->transService->validationCni($transactionTab['clientDepot']['cni'])==false){
                return $this->json('votre numero CNI n est pas correct',Response::HTTP_NOT_FOUND);
            }
            $clientDepot->setCni($transactionTab['clientDepot']['cni']);
            //dd($clientDepot);
            if ($this->transService->validationphone($transactionTab['clientDepot']['phoneEmetteur'])==false){
                return $this->json('votre telephone n est pas correct',Response::HTTP_NOT_FOUND);
            }
            $clientDepot->setTelephone($transactionTab['clientDepot']['phoneEmetteur']);
            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($clientDepot);
            $transaction->setClientDepot($clientDepot);
            //dd($clientDepot);

           
            $clientRetrait->setNom($transactionTab['clientRetrait']['nomBen']);
            $clientRetrait->setPrenom($transactionTab['clientRetrait']['prenomBen']);
            
            if ($this->transService->validationPhone($transactionTab['clientRetrait']['phoneBen'])==false){
                return $this->json('votre telephone n est pas correct',Response::HTTP_NOT_FOUND);
            }
            $clientRetrait->setTelephone($transactionTab['clientRetrait']['phoneBen']);
            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($clientRetrait);
            $transaction->setClientRetrait($clientRetrait);
            //dd($clientRetrait);

            $entityManager= $this->getDoctrine()->getManager();
            $entityManager->persist($transaction);
            //dd($transaction);
            $entityManager->flush();
            return $this->json($transaction->getCodeTrans());
        }
        return $this->json('solde insuffisant pour cette transaction', Response::HTTP_NOT_FOUND);
    }
//****************************************function getcompte de transaction************************** */
    /**
     * @Route
     *("api/admin/compte/transactions", methods={"GET"},
     *name="getTransCompte",
     * )
     */
    public function getTransCompte(Security $security)
    {

        //dd($security->getUser());
        $user= $security->getUser();
        $compteId= $user->getAgence()->getCompte()->getSolde();
        //dd($compteId);
        return $this->json($compteId);

}

//*******************/function infod du code***************************************

   /**
     * @Route
     *(path="/api/user/transaction/{codeTrans}", methods={"GET"},
     * )
     */
    public function codeTrans( TransactionRepository $transactionRepository, $codeTrans)
    {

        $codeTrans = $transactionRepository->findOneBy(['codeTrans'=>$codeTrans]);
        return $this->json( $codeTrans );
       //dd($codeTrans);

    }

//****************************************partie Retrait***********************************************
     /**
     * @Route
     *(path="/api/user/transactions/retrait", name="retrait", methods={"PUT"},
     * )
     */
     public function retrait(Request $request,SerializerInterface $serializer, EntityManagerInterface $entityManager, Security $security,
                                     ValidatorInterface $validator, UserRepository $userRepository, TransactionRepository $transactionRepository, TransService $transService):Response
    {

        
        $transactionJson= $request->getContent();
        $transactionTab= $serializer->decode($transactionJson, 'json');
        $clientRetrait= $transactionTab['clientRetrait']['cni'];
        //dd($clientRetrait);
        $transaction= $transactionRepository->findOneBy(['codeTrans'=>$transactionTab['codeTrans']]);
        //dd($security->getUser()->getAgence()->getCompte());
        //dd($transaction);
        if(($transaction->getDateRetrait()==null)){
                if ($this->transService->validationCni($transactionTab['clientRetrait']['cni'])==false){
                 return $this->json('votre numero CNI n est pas correct',Response::HTTP_NOT_FOUND);
            }

            $userRetrait=$this->getUser();
            //dd($userRetrait);
            $montantRetrait=$transaction->getMontant();
            $compteAgenceRetrait=$security->getUser()->getAgence()->getCompte();
            //dd($compteAgenceRetrait);
            $soldeCompteAgenceRetrait = $compteAgenceRetrait->getSolde();
            //dd($soldeCompteAgenceRetrait);
        
            $compteAgenceRetrait->setSolde($soldeCompteAgenceRetrait+$montantRetrait);
            $entityManager=$this->getDoctrine()->getManager();
            $transaction->setUserRetrait($userRetrait);
            $transaction->getClientRetrait()->setCni($clientRetrait);
            $transaction->setCompte($compteAgenceRetrait);
            //dd($transaction);
            $transaction->setDateRetrait(new \DateTime());
            $entityManager->persist($transaction);
            $entityManager->flush();
        }
        return $this->json( $transaction, 200, [],['groups'=>'retrait_red']);
    }
}