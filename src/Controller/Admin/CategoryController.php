<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Gallery;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/category')]
final class CategoryController extends AbstractController
{
    #[Route(name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie "' . $category->getTitle() . '" a été créée avec succès !');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie "' . $category->getTitle() . '" a été modifiée avec succès !');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        // Récupérer les galeries triées par position
        $galleries = $entityManager->getRepository(Gallery::class)
            ->createQueryBuilder('g')
            ->where('g.category = :category')
            ->setParameter('category', $category)
            ->orderBy('g.position', 'ASC')
            ->addOrderBy('g.id', 'ASC') // Tri secondaire par ID pour assurer un ordre stable
            ->getQuery()
            ->getResult();

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
            'galleries' => $galleries,
        ]);
    }


    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $categoryName = $category->getTitle(); // Sauvegarder le nom avant suppression
                $entityManager->remove($category);
                $entityManager->flush();

                $this->addFlash('success', 'La catégorie "' . $categoryName . '" a été supprimée avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression de la catégorie. Elle est peut-être utilisée par des galeries.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide. Veuillez réessayer.');
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/update-gallery-order', name: 'app_category_update_gallery_order', methods: ['POST'])]
    public function updateGalleryOrder(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['galleryOrder']) || !is_array($data['galleryOrder'])) {
            return $this->json(['error' => 'Invalid data format'], Response::HTTP_BAD_REQUEST);
        }

        $galleryRepository = $entityManager->getRepository(Gallery::class);

        foreach ($data['galleryOrder'] as $position => $galleryId) {
            $gallery = $galleryRepository->find($galleryId);

            if ($gallery && $gallery->getCategory()->getId() === $category->getId()) {
                $gallery->setPosition($position);
            }
        }

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

}
