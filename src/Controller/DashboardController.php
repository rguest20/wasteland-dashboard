<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Location;
use App\Entity\Npc;
use App\Entity\Role;
use App\Form\LocationType;
use App\Form\NpcType;
use App\Form\RoleType;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard_home', methods: ['GET'])]
    #[Route('/dashboard', name: 'dashboard_index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/locations/new', name: 'location_new', methods: ['GET', 'POST'])]
    public function newLocation(Request $request, EntityManagerInterface $em): Response
    {
        $location = new Location();

        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($location);
            $em->flush();

            $this->addFlash('success', 'Location created.');

            return $this->redirectToRoute('dashboard_index'); // or a location list page
        }

        return $this->render('location/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/roles/new', name: 'role_new', methods: ['GET', 'POST'])]
    public function newRole(Request $request, EntityManagerInterface $em): Response
    {
        $role = new Role();

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($role);
            $em->flush();

            $this->addFlash('success', 'Role created.');

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('role/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/npcs/new', name: 'npc_new', methods: ['GET', 'POST'])]
    public function newNpc(Request $request, EntityManagerInterface $em): Response
    {
        $npc = new Npc();

        $form = $this->createForm(NpcType::class, $npc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
            $npc->setCreatedAt($now);
            $npc->setUpdatedAt($now);

            $em->persist($npc);
            $em->flush();

            $this->addFlash('success', 'NPC created.');

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('npc/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/npcs/{id}', name: 'dashboard_npc', methods: ['GET'])]
    #[Route('/locations/{id}', name: 'dashboard_location', methods: ['GET'])]
    #[Route('/roles/{id}', name: 'dashboard_role', methods: ['GET'])]
    public function entityDetail(): Response
    {
        return $this->render('dashboard/detail.html.twig');
    }
}
