<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category/{slug}', name: 'app_category')]
    public function show(
        string                 $slug,
        CategoryRepository     $categoryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas.');
        }

        // Récupérer les galeries triées par position
        $galleries = $entityManager->getRepository(Gallery::class)
            ->createQueryBuilder('g')
            ->where('g.category = :category')
            ->setParameter('category', $category)
            ->orderBy('g.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('category/index.html.twig', [
            'category' => $category,
            'galleries' => $galleries,
        ]);
    }
}
