<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use App\Form\GalleryForm;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/gallery')]
final class GalleryController extends AbstractController
{
    #[Route(name: 'app_gallery_index', methods: ['GET'])]
    public function index(GalleryRepository $galleryRepository): Response
    {
        return $this->render('admin/gallery/index.html.twig', [
            'galleries' => $galleryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gallery_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryForm::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image mise en avant
            $featuredImageFile = $form->get('featuredImageFile')->getData();
            if ($featuredImageFile) {
                $gallery->setFeaturedImageFile($featuredImageFile);
            }

            $entityManager->persist($gallery);
            $entityManager->flush();

            $this->addFlash('success', 'La gallery "' . $gallery->getTitle() . '" a été créée avec succès !');
            return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gallery/new.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gallery_show', methods: ['GET'])]
    public function show(Gallery $gallery): Response
    {
        return $this->render('admin/gallery/show.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gallery_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GalleryForm::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image mise en avant
            $featuredImageFile = $form->get('featuredImageFile')->getData();
            if ($featuredImageFile) {
                $gallery->setFeaturedImageFile($featuredImageFile);
            }

            $entityManager->flush();

            $this->addFlash('success', 'La gallery "' . $gallery->getTitle() . '" a été modifiée avec succès !');
            return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gallery/edit.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
            'tinymce_api_key' => $this->getParameter('tinymce_api_key'),
        ]);
    }

    #[Route('/{id}', name: 'app_gallery_delete', methods: ['POST'])]
    public function delete(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $gallery->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $galleryName = $gallery->getTitle(); // Sauvegarder le nom avant suppression
                $entityManager->remove($gallery);
                $entityManager->flush();

                $this->addFlash('success', 'La gallery "' . $galleryName . '" a été supprimée avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression de la gallery.');
            }
        } else {
            $this->addFlash('error', 'Token CSRF invalide. Veuillez réessayer.');
        }

        return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
    }
}
