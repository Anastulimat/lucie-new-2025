<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Handler\UploadHandler;

class ImageController extends AbstractController
{
    #[Route('admin/image/{id}/delete', name: 'app_image_delete', methods: ['POST', 'DELETE'])]
    public function deleteImage(
        Request $request,
        Image $image,
        EntityManagerInterface $entityManager,
        UploadHandler $uploadHandler
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->getPayload()->getString('_token'))) {

            // Supprimer le fichier physique lié à l'image
            $uploadHandler->remove($image, 'imageFile');

            // Ensuite, supprimer l'entité
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
    }

}
