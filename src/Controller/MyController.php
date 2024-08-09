<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Photo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class MyController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/my/photos', name: 'my_photos')]
    public function index()
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User is not authenticated');
        }

        $myPhotos = $this->entityManager->getRepository(Photo::class)->findBy(['user' => $user]);

        return $this->render('my/index.html.twig',[
            'myPhotos' => $myPhotos
        ]);
    }

    #[Route('/my/photos/set_private/{id}', name: 'my_photos_set_as_private')]
    /**
     * @param int $id
     */
    public function myPhotoSetAsPrivate(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $photoRepository = $entityManager->getRepository(Photo::class);
        $myPhoto = $photoRepository->find($id);

        if (!$myPhoto) {
            throw $this->createNotFoundException('Photo not found.');
        }

        if ($this->getUser() !== $myPhoto->getUser()) {
            throw new AccessDeniedException('You do not have permission to edit this photo.');
        }
        try {
            $myPhoto->setIsPublic(false);
            $entityManager->persist($myPhoto);
            $entityManager->flush();
            $this->addFlash('success', 'Photo set as private');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error setting photo as private');
        }

        return $this->redirectToRoute('my_photos');
    }
    #[Route('/my/photos/set_public/{id}', name: 'my_photos_set_as_public')]
    /**
     * @param int $id
     */
    public function myPhotoSetAsPublic(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $photoRepository = $entityManager->getRepository(Photo::class);
        $myPhoto = $photoRepository->find($id);

        if (!$myPhoto) {
            throw $this->createNotFoundException('Photo not found.');
        }

        if ($this->getUser() !== $myPhoto->getUser()) {
            throw new AccessDeniedException('You do not have permission to edit this photo.');
        }

        try {
            $myPhoto->setIsPublic(true);
            $entityManager->persist($myPhoto);
            $entityManager->flush();
            $this->addFlash('success', 'Photo set as private');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error setting photo as private');
        }

        return $this->redirectToRoute('my_photos');
    }

    #[Route('/my/photos/remove/{id}', name: 'my_photos_remove')]
    public function myPhotosRemove(int $id, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        $myPhoto = $entityManager->getRepository(Photo::class)->find($id);

        if ($this->getUser() === $myPhoto->getUser()) {
            $filePath = 'image/hosting/' . $myPhoto->getFilename();
    
            if ($filesystem->exists($filePath)) {
                try {
                    $filesystem->remove($filePath);
                    $this->addFlash('success', 'File removed from disk');
                } catch (IOExceptionInterface $exception) {
                    $this->addFlash('error', 'Error removing photo: ' . $exception->getMessage());
                    return $this->redirectToRoute('latest_photos');
                }
            } else {
                $this->addFlash('error', 'File does not exist');
            }
    
            $entityManager->remove($myPhoto);
            $entityManager->flush();
            $this->addFlash('success', 'Photo removed from database');
        } else {
            $this->addFlash('error', 'You are not authorized to remove this photo');
        }

        return $this->redirectToRoute('my_photos');
    }
}