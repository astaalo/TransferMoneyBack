<?php
namespace App\DataPersister;

use App\Entity\Transaction;
use App\Service\TransService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class TransDataPersister implements ContextAwareDataPersisterInterface
{
   /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $transService;
    private $repo;


    public function __construct(EntityManagerInterface $em, TransService $transService, TransactionRepository $repo){
        $this->entityManager=$em;
        $this->transService=$transService;
        $this->repo=$repo;

    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Transaction;
    }

    public function persist($data, array $context = [])
    {      
        if ($data->getType()=='Depot') {
            # code...
            $code=($this->transService->Code());
            $data->setCodeTrans(wordwrap($code, 3, "-",  true));
            $data->setDateDepot(new \DateTime);
            $frais=$this->transService->CalculFrais($data->getMontant());
            $data->setFrais($frais);
            $parts=$this->transService->CalculPart($frais);
            //dd($parts);
            $data->setFraisEtat($parts['PartEtat']);
            $data->setFraisSystem($parts['PartSystem']);
            $data->setFraisDepot($parts['PartDepot']);
            $data->setFraisRetrait($parts['PartRetrait']);
            $solde=($data->getCompte()->getSolde() + $parts['PartDepot']) - $data->getMontant();
            //dd($solde);
            $data->getCompte()->setSolde($solde);
            //dd($data);
             $this->entityManager->persist($data);
             $this->entityManager->flush(); 
             return new JsonResponse("Depot effectué avec succes", Response::HTTP_CREATED, [], true);
        }

        elseif ($data->getType()=='Retrait') {
            $verify = new Transaction();
            $verify=$this->repo->findOneBy(["code_trans" => $data->getCodeTrans()]);
            //dd($verify);
               if($verify){
                    if ($data->getType()!=$verify->getType()) {
                        $code=($this->transService->Code());
                        $data->setDateRetrait(new \DateTime);
                        $frais=$this->transService->CalculFrais($data->getMontant());
                        $data->setFrais($frais);
                        $parts=$this->transService->CalculPart($frais);
                        $data->setPartEtat($parts['PartEtat']);
                        $data->setPartSystem($parts['PartSystem']);
                        $data->setPartDepot($parts['PartDepot']);
                        $data->setPartRetrait($parts['PartRetrait']);
                        $solde=($data->getCompte()->getSolde() + $parts['PartDepot']) + $data->getMontant();
                        $data->getCompte()->setSolde($solde);
                        $this->entityManager->persist($data);
                        $this->entityManager->flush();
                        return new JsonResponse("Retrait effectuée avec succes", Response::HTTP_CREATED, [], true); 
                        }
                    else{
                        return new JsonResponse("Cette transaction a été retiré", Response::HTTP_FORBIDDEN, [], true);
                    }
               }
               else {
                return new JsonResponse("Code de transaction n'existe pas", Response::HTTP_NOT_FOUND, [], true);
               }            
        }
        else {
            return new JsonResponse("Veuillez choisir un type de transaction", Response::HTTP_NOT_FOUND, [], true);
        } 
    }
  

public function remove($data, array $context = [])
    {
        $etat=$data;
        if ($etat->gettype()=="Depot") {
            $etat->setEtatTrans(true);
            $etat->setCodeTrans('annule');
            $this->entityManager->persist($etat);
            $this->entityManager->flush();
            return new JsonResponse("Transaction annulee avec succes", Response::HTTP_OK, [], true);
        } 
    }
}