<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Compte;
use App\Repository\AgenceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompteDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AgenceRepository
     */
    private $agenceRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, AgenceRepository $agenceRepository, UserRepository $userRepository) {
        $this->entityManager = $entityManager;
        $this->agenceRepository = $agenceRepository;
        $this->userRepository = $userRepository;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
    }

    public function persist($data, array $context = [])
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        $data->setArchived(true);
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
