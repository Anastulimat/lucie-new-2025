<?php

namespace App\Controller\Admin;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class DashboardController extends AbstractController
{

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    #[Route(name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        // Vérifier si c'est juste après une connexion
        $session = $this->getParameter('kernel.environment') === 'test' ? null : $this->container->get('request_stack')->getSession();

        if ($session && !$session->has('login_welcome_shown')) {
            $this->addFlash('success', 'Connexion réussie ! Bienvenue dans l\'administration.');
            $session->set('login_welcome_shown', true);
        }

        return $this->render('admin/dashboard/index.html.twig');
    }
}
