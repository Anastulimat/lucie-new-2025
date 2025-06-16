<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Handler\UploadHandler;

class ImageController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('admin/image/{id}/delete', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Image $image, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Essayer de lire JSON d'abord, puis les données POST
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            // Si pas de JSON, lire les données POST classiques
            $token = $request->request->get('_token');
        } else {
            $token = $data['_token'] ?? null;
        }

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $token)) {
            $entityManager->remove($image);
            $entityManager->flush();
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token invalid'], 400);
        }
    }

    #[Route('admin/gallery/{id}/reorder', name: 'app_gallery_reorder_images', methods: ['POST'])]
    public function reorderImages(Request $request, Gallery $gallery, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->isCsrfTokenValid('reorder_images_' . $gallery->getId(), $request->headers->get('X-CSRF-Token'))) {
            return new JsonResponse(['success' => false, 'error' => 'Token CSRF invalide'], 400);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['imageIds']) || !is_array($data['imageIds'])) {
            return new JsonResponse(['success' => false, 'error' => 'Données invalides'], 400);
        }

        try {
            $gallery->reorderImages($data['imageIds']);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => 'Erreur lors de la réorganisation'], 500);
        }
    }

}
