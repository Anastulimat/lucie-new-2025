<?php

namespace App\Controller;

use App\Entity\Image;
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

        /** @var Image[] $shuffledImages */
        $shuffledImages = $images->toArray();
        shuffle($shuffledImages); // Mélange aléatoire

        return $this->render('home/index.html.twig', [
            'images' => $shuffledImages,
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    #[Route('/test', name: 'app_test')]
    public function test(): Response
    {
        return $this->render('home/test.html.twig');
    }
}
