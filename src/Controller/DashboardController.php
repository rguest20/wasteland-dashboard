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
use App\Entity\WorldSecret;
use App\Form\LocationType;
use App\Form\NpcType;
use App\Form\RoleType;
use App\Form\WorldSecretType;

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

    #[Route('/locations/{id}/update', name: 'location_edit', methods: ['GET', 'PATCH', 'POST'])]
    public function editLocation(Location $location, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LocationType::class, $location, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Location updated.');

            return $this->redirectToRoute('dashboard_location', ['id' => $location->getId()]);
        }

        return $this->render('location/edit.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
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

    #[Route('/roles/{id}/update', name: 'role_edit', methods: ['GET', 'PATCH', 'POST'])]
    public function editRole(Role $role, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RoleType::class, $role, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Role updated.');

            return $this->redirectToRoute('dashboard_role', ['id' => $role->getId()]);
        }

        return $this->render('role/edit.html.twig', [
            'form' => $form->createView(),
            'role' => $role,
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

    #[Route('/npcs/{id}/update', name: 'npc_edit', methods: ['GET', 'PATCH', 'POST'])]
    public function editNpc(Npc $npc, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NpcType::class, $npc, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $npc->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $em->flush();

            $this->addFlash('success', 'NPC updated.');

            return $this->redirectToRoute('dashboard_npc', ['id' => $npc->getId()]);
        }

        return $this->render('npc/edit.html.twig', [
            'form' => $form->createView(),
            'npc' => $npc,
        ]);
    }

    #[Route('/worldsecrets/new', name: 'world_secret_new', methods: ['GET', 'POST'])]
    public function newWorldSecret(Request $request, EntityManagerInterface $em): Response
    {
        $worldSecret = new WorldSecret();

        $form = $this->createForm(WorldSecretType::class, $worldSecret);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($worldSecret);
            $em->flush();

            $this->addFlash('success', 'World secret created.');

            return $this->redirectToRoute('dashboard_index');
        }

        return $this->render('worldsecret/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/worldsecrets/{id}/update', name: 'world_secret_edit', methods: ['GET', 'PATCH', 'POST'])]
    public function editWorldSecret(WorldSecret $worldSecret, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(WorldSecretType::class, $worldSecret, [
            'method' => 'PATCH',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'World secret updated.');

            return $this->redirectToRoute('dashboard_world_secret', ['id' => $worldSecret->getId()]);
        }

        return $this->render('worldsecret/edit.html.twig', [
            'form' => $form->createView(),
            'worldSecret' => $worldSecret,
        ]);
    }

    #[Route('/npcs/{id}', name: 'dashboard_npc', methods: ['GET'])]
    #[Route('/locations/{id}', name: 'dashboard_location', methods: ['GET'])]
    #[Route('/roles/{id}', name: 'dashboard_role', methods: ['GET'])]
    #[Route('/worldsecrets/{id}', name: 'dashboard_world_secret', methods: ['GET'])]
    public function entityDetail(): Response
    {
        return $this->render('dashboard/detail.html.twig');
    }
}
