<?php


namespace App\DataCollectionProvider;

use App\Entity\Transaction;
use App\Repository\UserRepository;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class TransactionCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $transactionRepository;
    private $tokenStorage;
    private $agenceRepo;
    private $compteRepo;
    private $userRepo;
     public function __construct(TransactionRepository $transactionRepository,UserRepository $userRepo,
     TokenStorageInterface $tokenStorage, AgenceRepository $agenceRepo, CompteRepository $compteRepo)
    {
        $this->transactionRepository = $transactionRepository;
        $this->tokenStorage =$tokenStorage;
        $this->agenceRepo =$agenceRepo;
        $this->userRepo =$userRepo;
        $this->compteRepo =$compteRepo;
    }
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Transaction::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        // $idAgence = $this->tokenStorage->getToken()->getUser()->getAgence()->getId();
        // $agence =  $this->agenceRepo->findOneBy(['id'=>$idAgence]);
        // $compte =  $this->compteRepo->findOneBy(['id'=>$agence->getCompte()->getId()]);
        // $solde = $compte->getSolde();
        // dd($solde);
        //recupere tous les profils
        $role = $this->tokenStorage->getToken()->getUser()->getRoles()[0];
        //dd($role);
        if($role === "ROLE_AdminSystem"){
            $transactions =  $this->transactionRepository->findAll();
            $data = [];
            $i =0;
            $t =0;
            foreach($transactions as $key =>$transaction ){
             $result[$i]['montant'] = $transaction->getMontant();
             if($transaction->getDateDepot()!= null){
                 $data[$i]['ttc'] = $transaction->getFrais();
                 $data[$i]['montant'] = $transaction->getMontant();
                 $data[$i]['id'] = $transaction->getId();
                 $data[$i]['date'] = $transaction->getDateDepot()->format('Y-m-d ');
                 $data[$i]['commission'] = $transaction->getFraisDepot();
                 $data[$i]['type'] = "depot";
                 $user = $this->userRepo->findOneBy(['id'=>$transaction->getUserDepot()->getId()]);
                 $nom = $user->getFirstname().' '.$user->getLastname();
                 $data[$i]['nom'] = $nom;
                }
             $i++;
             $t++;
     
            
            }
            foreach($transactions as $key =>$transaction ){
            $result[$i]['montant'] = $transaction->getMontant();
            if($transaction->getDateRetrait()!= null){
                $data[$i]['ttc'] = $transaction->getFrais();
                $data[$i]['montant'] = $transaction->getMontant();
                $data[$i]['id'] = $transaction->getId();
                $data[$i]['date'] = $transaction->getDateRetrait()->format('Y-m-d ');
                $data[$i]['commission'] = $transaction->getFraisRetrait();
                $data[$i]['type'] = "retrait";
                $user = $this->userRepo->findOneBy(['id'=>$transaction->getUserRetrait()->getId()]);
                $nom = $user->getFirstname().' '.$user->getLastname();
                $data[$i]['nom'] = $nom;
            }
            }
            $i++;
            $t++;

      }elseif($this->tokenStorage->getToken()->getUser()->getRoles()[0] === "ROLE_AdminAgent"){
        $i =0;
        $t =0;
        $compteid =  $this->tokenStorage->getToken()->getUser()->getAgence()->getCompte()->getId();
        $transactions =  $this->transactionRepository->findBy(['compte'=>$compteid]);

            
            foreach($transactions as $key => $transaction){

                if($transaction->getDateDepot() !=null){
                    $data[$i]['ttc'] = $transaction->getFrais();
                    $data[$i]['montant'] = $transaction->getMontant();
                    $data[$i]['id'] = $transaction->getId();
                    $data[$i]['date'] = $transaction->getDateDepot()->format('Y-m-d ');
                    $data[$i]['commission'] = $transaction->getFraisDepot();
                    $data[$i]['type'] = "depot";
                    $client = $this->userRepo->findOneBy(['id'=>$transaction->getUserDepot()->getId()]);
                    $nom = $client->getFirstname().' '.$client->getLastname();
                    $data[$i]['nom'] = $nom;
                }

                $i++;
                $t++;
            }
            foreach($transactions as $key => $transaction){

                if($transaction->getDateRetrait() !=null){
                    $data[$i]['ttc'] = $transaction->getFrais();
                    $data[$i]['montant'] = $transaction->getMontant();
                    $data[$i]['id'] = $transaction->getId();
                    // dd($transaction);
                    $data[$i]['date'] = $transaction->getDateRetrait()->format('Y-m-d');
                    $data[$i]['commission'] = $transaction->getFraisRetrait();
                    $data[$i]['type'] = "retrait";
                    $userAgence = $this->userRepo->findOneBy(['id'=>$transaction->getUserRetrait()->getId()]);
                    $nom = $userAgence->getFirstname().' '.$userAgence->getLastname();
                    $data[$i]['nom'] = $nom;

                }
                $i++;
                $t++;
            }

        }
    else{
        $i =0 ;
        $t = 0;
        $user = $this->tokenStorage->getToken()->getUser()->getId();
        $transactions = $this->transactionRepository->findBy(['userDepot'=>$user]);

        $transactionsR = $this->transactionRepository->findBy(['userRetrait'=>$user]);
        foreach($transactions as $key => $transaction){
            $data[$t]['ttc'] = $transaction->getFrais();
            $data[$t]['montant'] = $transaction->getMontant();
           if($transaction->getDateDepot() !=null){
              $data[$t]['date'] = $transaction->getDateDepot()->format('Y-m-d');
              $data[$t]['commission'] = $transaction->getFraisDepot();
              $data[$t]['type'] = "depot";
           }
           $t++;
           
        }
        foreach($transactionsR as $key => $trans){
            $data[$t]['montant'] = $trans->getMontant();
           if($trans->getDateRetrait() !=null){
               $data[$t]['commission'] = $transaction->getFraisRetrait();
              $data[$t]['date'] = $trans->getDateRetrait()->format('Y-m-d');
              $data[$t]['type'] = "retrait";
           }
        }
    }
       // dd($data);
       return $data;
    }
}