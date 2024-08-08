<?php

namespace App\Controller;

class LatestPhotoController
{
    #[Route('/', name: 'index')]
    public function index()
    {
        return $this->render('latest_photo/index.html.twig', [
            'controller_name' => 'LatestPhotoController',
        ]);
    }
}