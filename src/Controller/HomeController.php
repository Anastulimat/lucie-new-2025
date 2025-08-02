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
        //$shuffledImages = $images->toArray();
        //shuffle($shuffledImages); // Mélange aléatoire

        $response = new Response();
        $response->setCache([
            'max_age' => 3600, // 1 heure
            'public' => true,
        ]);

        return $this->render('home/index.html.twig', [
            'images' => $images,
            'controller_name' => 'HomeController',
        ], $response);
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
