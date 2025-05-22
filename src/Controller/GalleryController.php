<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalleryController extends AbstractController
{
    #[Route('/gallery/{id}/{slug}', name: 'app_gallery', requirements: ['id' => '\d+'])]
    public function show(int $id, string $slug, GalleryRepository $galleryRepository): Response
    {
        $gallery = $galleryRepository->findOneBy(['id' => $id, 'slug' => $slug]);

        if (!$gallery) {
            throw $this->createNotFoundException('La galerie demandée n\'existe pas.');
        }

        return $this->render('gallery/index.html.twig', [
            'gallery' => $gallery,
        ]);
    }

}
