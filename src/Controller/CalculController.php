<?php

namespace App\Controller;

use App\Service\TransService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalculController extends AbstractController
{
    /**
     * @Route
     *(path="/api/user/frais/{montant}", methods={"GET"}
     * )
     */
    public function getFraisMontant(Request $request,TransactionRepository $transactionRepository,SerializerInterface $serializer, EntityManagerInterface $entityManager, TransService $transService,$montant)
    {
        //dd($montant);
        $frais=$transService->frais($montant);
        $fraisEtat=$transService->fraisEtat($frais);
        $fraisDepot=$transService->fraisDepot($frais);
        $fraisRetrait=$transService->fraisRetrait($frais);

        $fraisTab['frais']= $frais;
        $fraisTab['fraisEtat']= $fraisEtat;
        $fraisTab['fraisDepot']= $fraisDepot;
        $fraisTab['fraisRetrait']= $fraisRetrait;


        return $this -> json($fraisTab, Response::HTTP_OK,);


    }
}
