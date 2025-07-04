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

        return $this->render('gallery/gallery-v2.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery-v2/{id}/{slug}', name: 'app_gallery_v2', requirements: ['id' => '\d+'])]
    public function displayV2(int $id, string $slug, GalleryRepository $galleryRepository): Response
    {
        $gallery = $galleryRepository->findOneBy(['id' => $id, 'slug' => $slug]);

        if (!$gallery) {
            throw $this->createNotFoundException('La galerie demandée n\'existe pas.');
        }

        return $this->render('gallery/gallery-v2.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery-v3/{id}/{slug}', name: 'app_gallery_v3', requirements: ['id' => '\d+'])]
    public function displayV3(int $id, string $slug, GalleryRepository $galleryRepository): Response
    {
        $gallery = $galleryRepository->findOneBy(['id' => $id, 'slug' => $slug]);

        if (!$gallery) {
            throw $this->createNotFoundException('La galerie demandée n\'existe pas.');
        }

        return $this->render('gallery/gallery-v3.html.twig', [
            'gallery' => $gallery,
        ]);
    }

}
