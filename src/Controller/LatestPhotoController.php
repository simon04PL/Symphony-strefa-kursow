<?php

namespace App\Controller;

use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LatestPhotoController extends AbstractController
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/latest', name: 'latest_photos')]
    public function index(): Response
    {
        // Zakładam, że metoda `findAllPublic` jest zdefiniowana w repozytorium `PhotoRepository`
        $latestPhotosPublic = $this->entityManager->getRepository(Photo::class)->findAllPublic();

        return $this->render('latest_photos/index.html.twig', [
            'latestPhotosPublic' => $latestPhotosPublic,
        ]);
    }
}
