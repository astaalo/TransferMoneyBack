<?php

namespace App\Service;

use App\Repository\GrilleTarifRepository;

class TarifService
{
    private $taxe;
    private $grilleTarifRepository;
    public function __construct(GrilleTarifRepository $grilleTarifRepository)
    {
        $this->grilleTarifRepository=$grilleTarifRepository;
    }

    public function Tarif(int $montant){
        $taxes = $this->grilleTarifRepository->findAll();
        foreach ($taxes as $tax) {
            switch (true){
                case($montant >= $tax->getMin() && $montant < $tax->getMax()):
                    $this->taxe = $tax->getTarif();
                    if($this->taxe == 0.02){
                        $this->taxe *=$montant;
                    }
            }
        }
        return $this->taxe;
    }

    // function generateCode($length = 9) {
    //     $characters = '0123456789';
    //     $charactersLength = strlen($characters);
    //     $randomString = '';
    //     for ($i = 0; $i < $length; $i++) {
    //         $randomString .= $characters[rand(0, $charactersLength - 1)];
    //     }
    //     return $randomString;
    // }

}
