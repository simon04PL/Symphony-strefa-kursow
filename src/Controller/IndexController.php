<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UploadPhotoType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, EntityManagerInterface $em, KernelInterface $kernel): Response
    {
        $form = $this->createForm(UploadPhotoType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
    if ($this->getUser()) {
        $pictureFileName = $form->get('filename')->getData();
        if ($pictureFileName) {
            try {
                $oryginalFileName = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = iconv('UTF-8', 'ASCII//TRANSLIT', $oryginalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $pictureFileName->guessExtension();
                $uploadDir = $kernel->getProjectDir() . '/public/images/hosting';
                $pictureFileName->move($uploadDir, $newFileName);
    
                $entityPhotos = new Photo();
                $entityPhotos->setFilename($newFileName);
                $entityPhotos->setIsPublic($form->get('is_public')->getData());
                $entityPhotos->setUploadedAt(new \DateTime());
                $entityPhotos->setUser($this->getUser());
    
                $em->persist($entityPhotos);
                $em->flush();
    
                $this->addFlash('success', 'Plik został dodany');
    
                return $this->redirectToRoute('index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Błąd podczas dodawania pliku');
            }
            
        }
    }
}

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
