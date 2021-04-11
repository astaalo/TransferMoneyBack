<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Entity\Caissier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepotCaissierController extends AbstractController
{

    /**
     * @Route("/admin/caissiers/depot", name="depot_caissier", methods={"POST"})
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param CommissionRepository $commissionRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function postCaissier(Request $request,
                              TokenStorageInterface $tokenStorage,
                              EntityManagerInterface $manager,
                              SerializerInterface $serializer
    ){

        $adminAgence= $tokenStorage->getToken()->getUser();
        $compte= $adminAgence->getAgence()->getCompte();
        if ($compte->getSolde() < 5000){
            return $this->json("le depot ne peut pas etre effectué car le solde du compte est inferieur à 5000f",403);
        }
       
        $data=$request->getContent();
        $dataTab= $serializer->decode($data,'json');
        $dataObject= $this->repo->FindOneBy(["id" => $id]);
        $montant = $dataObject->getSolde();
        if($data['solde']>0){
            $result = [];
            $montant = $data['solde'] + $montant;
            $dataObject->setSolde[$montant];
            //dd($dataObject);
            $this->manager->persist[$dataObject];
            $this->manager->flush();
            return new JsonResponse('Depot effectué avec succés', Response::HTTP_OK);
        }
        else{
            return new JsonResponse("Une erreur sest produite lors du depot", Response::HTTP_BAD_REQUEST, [], true);
        }
    }
}
