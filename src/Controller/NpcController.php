<?php

namespace App\Controller;

use App\Dto\CreateNpcRequest;
use App\Entity\Npc;
use App\Service\LocationService;
use App\Service\NpcService;
use App\Service\RoleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/npcs')]
class NpcController extends AbstractController
{
    #[Route('', name: 'npc_list', methods: ['GET'])]
    public function list(NpcService $npcService): JsonResponse
    {
        $npcs = $npcService->getAllNpcs();

        $result = array_map(function (Npc $npc) {
            return [
                'id' => $npc->getId(),
                'name' => $npc->getName(),
                'notes' => $npc->getNotes(),
                'role' => $npc->getRole()?->getName(),
                'location' => $npc->getLocation()?->getName(),
                'created_at' => $npc->getCreatedAt()?->format(DATE_ATOM),
            ];
        }, $npcs);

        return new JsonResponse($result);
    }

    #[Route('/{id}', name: 'npc_get', methods: ['GET'])]
    public function get(Npc $npc): JsonResponse
    {
        return new JsonResponse([
            'id' => $npc->getId(),
            'name' => $npc->getName(),
            'notes' => $npc->getNotes(),
            'role' => $npc->getRole()?->getName(),
            'location' => $npc->getLocation()?->getName(),
            'created_at' => $npc->getCreatedAt()?->format(DATE_ATOM),
            'updated_at' => $npc->getUpdatedAt()?->format(DATE_ATOM),
        ]);
    }

    #[Route('', name: 'npc_create', methods: ['POST'])]
    public function create(
        Request $request,
        NpcService $npcService,
        RoleService $roleService,
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $dto = new CreateNpcRequest();
        try {
            $dto->loadData($payload);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $errors = $npcService->validateCreateNpcRequest($dto);
        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $role = $roleService->getRoleByName($dto->role_name ?? '');
        if (!$role) {
            return new JsonResponse(
                ['errors' => ['role_name' => ['Unknown role_name']]],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $npc = $npcService->createNpc($dto, $role);

        return new JsonResponse([
            'id' => $npc->getId(),
            'name' => $npc->getName(),
            'notes' => $npc->getNotes(),
            'role' => $npc->getRole()?->getName(),
            'location' => $npc->getLocation()?->getName(),
            'created_at' => $npc->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $npc->getUpdatedAt()->format(DATE_ATOM),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'npc_update', methods: ['PUT'])]
    public function update(Npc $npc, Request $request, NpcService $npcService, RoleService $roleService): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['notes'])) {
            return new JsonResponse(['error' => 'Missing notes field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $newRole = $npc->getRole();
        if (array_key_exists('role_name', $payload)) {
            $roleName = is_string($payload['role_name']) ? trim($payload['role_name']) : '';
            $newRole = $roleService->getRoleByName($roleName);
            if (!$newRole) {
                return new JsonResponse(
                    ['errors' => ['role_name' => ['Unknown role_name']]],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        }

        $npc->setName($payload['name'] ?? $npc->getName());
        $npc->setNotes($payload['notes']);
        $npc->setRole($newRole);
        $npc->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        $npcService->saveNpc($npc);

        return new JsonResponse([
            'id' => $npc->getId(),
            'name' => $npc->getName(),
            'notes' => $npc->getNotes(),
            'role' => $npc->getRole()?->getName(),
            'location' => $npc->getLocation()?->getName(),
            'created_at' => $npc->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $npc->getUpdatedAt()->format(DATE_ATOM),
        ]);
    }

    #[Route('/{id}/location', name: 'npc_move', methods: ['PUT'])]
    public function move(
        Npc $npc,
        Request $request,
        NpcService $npcService,
        LocationService $locationService
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('location_id', $payload)) {
            return new JsonResponse(['error' => 'Missing location_id field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $locationId = $payload['location_id'];
        if (!is_int($locationId) && !is_null($locationId)) {
            return new JsonResponse(['error' => 'location_id must be an integer or null'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $location = null;
        if (is_int($locationId)) {
            $location = $locationService->getLocationById($locationId);
            if (!$location) {
                return new JsonResponse(['error' => 'Location not found'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        $npc->setLocation($location);
        $npc->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        $npcService->saveNpc($npc);

        return new JsonResponse([
            'id' => $npc->getId(),
            'location' => $npc->getLocation()?->getName(),
            'updated_at' => $npc->getUpdatedAt()?->format(DATE_ATOM),
        ]);
    }

    #[Route('/{id}', name: 'npc_delete', methods: ['DELETE'])]
    public function delete(int $id, NpcService $npcService): JsonResponse
    {
        $npc = $npcService->getNpcById($id);
        if (!$npc) {
            return new JsonResponse(['error' => 'NPC not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $npcService->deleteNpc($npc);

        return new JsonResponse([
            'message' => 'NPC deleted successfully',
        ]);
    }
}
