<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GalleryRepository $galleryRepository): Response
    {
        $homeGallery = $galleryRepository->findOneBy(['slug' => 'home']);
        $images = $homeGallery->getImages();

        $sortedImages = $images->toArray();
        usort($sortedImages, function($a, $b) {
            return strcmp($a->getId(), $b->getId());
        });

        return $this->render('home/index.html.twig', [
            'images' => $sortedImages,
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }
}
