<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Compte;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class AgenceDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var UserRepository
     */
    private $usersRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $usersRepository) {
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
    }

    public function persist($data, array $context = [])
    {
        $id = $data->getId();
        if ($data->setArchived(false)) {
            $utilisateurs = $this->usersRepository->findBy(['profil' => $id]);
            foreach ($utilisateurs as $utilisateur) {
                $utilisateur->setArchived(false);
                $this->entityManager->persist($utilisateur);
            }
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        $utilisateur = $data->getUsers();
        foreach ($utilisateur as $item) {
            $item->setArchived(true);
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
