<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    public function findAllPublic(): array
    {
        return $this->createQueryBuilder('photo')
            ->where('photo.is_public = 1') 
            ->orderBy('photo.uploaded_at', 'DESC') 
            ->getQuery()
            ->getResult();
    }
}
