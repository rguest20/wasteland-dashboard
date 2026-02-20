<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard_home', methods: ['GET'])]
    #[Route('/dashboard', name: 'dashboard_index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/npcs/{id}', name: 'dashboard_npc', methods: ['GET'])]
    #[Route('/locations/{id}', name: 'dashboard_location', methods: ['GET'])]
    #[Route('/roles/{id}', name: 'dashboard_role', methods: ['GET'])]
    public function entityDetail(): Response
    {
        return $this->render('dashboard/detail.html.twig');
    }
}
