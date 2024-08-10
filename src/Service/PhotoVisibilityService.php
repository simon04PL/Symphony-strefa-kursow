<?php

namespace App\Service;

use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security; 
use App\Entity\Photo;

class PhotoVisibilityService
{
    private $photoRepository;
    private $security;
    private $entityManager;

    public function __construct(PhotoRepository $photoRepository, Security $security, EntityManagerInterface $entityManager)
    {
        $this->photoRepository = $photoRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function makeVisible(int $id, bool $visibility): bool
    {
        $photo = $this->photoRepository->find($id);

        if ($photo === null) {
            return false; 
        }

        if ($this->isPhotoBelongToCurrentUser($photo)) {
            $photo->setIsPublic($visibility);
            $this->entityManager->persist($photo);
            $this->entityManager->flush();
            return true;
        }

        return false;
    }

    private function isPhotoBelongToCurrentUser(Photo $photo): bool
    {
        return $photo->getUser() === $this->security->getUser();
    }
}
