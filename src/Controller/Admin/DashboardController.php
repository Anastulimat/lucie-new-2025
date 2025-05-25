<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
final class DashboardController extends AbstractController
{

    #[Route(name: 'app_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        // Liste des entités disponibles pour la navigation
        $entities = [
            ['name' => 'Users', 'route' => 'app_user_index'],
            ['name' => 'Products', 'route' => 'app_product_index'],
            ['name' => 'Orders', 'route' => 'app_order_index'],
        ];

        return $this->render('admin/dashboard/index.html.twig', [
            'entities' => $entities,
        ]);
    }
}
