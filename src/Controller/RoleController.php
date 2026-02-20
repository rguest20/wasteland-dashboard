<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\RoleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/roles')]
final class RoleController
{
    #[Route('', name: 'role_list', methods: ['GET'])]
    public function list(RoleService $roleService): JsonResponse
    {
        $roles = array_map(
            fn (Role $role) => $this->serializeRole($role),
            $roleService->getAllRoles()
        );

        return new JsonResponse($roles);
    }

    #[Route('/{id}', name: 'role_get', methods: ['GET'])]
    public function get(int $id, RoleService $roleService): JsonResponse
    {
        $role = $roleService->getRoleById($id);
        if (!$role) {
            return new JsonResponse(['error' => 'Role not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->serializeRole($role));
    }

    #[Route('', name: 'role_create', methods: ['POST'])]
    public function create(Request $request, RoleService $roleService): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = $this->normalizeString($payload['name'] ?? null);
        if ($name === null) {
            return new JsonResponse(['error' => 'Missing name field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($roleService->getRoleByName($name)) {
            return new JsonResponse(['error' => 'Role name already exists'], JsonResponse::HTTP_CONFLICT);
        }

        $role = new Role();
        $role->setName($name);
        $role->setDescription($this->normalizeNullableString($payload['description'] ?? null));

        $roleService->saveRole($role);

        return new JsonResponse($this->serializeRole($role), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'role_update', methods: ['PUT'])]
    public function update(int $id, Request $request, RoleService $roleService): JsonResponse
    {
        $role = $roleService->getRoleById($id);
        if (!$role) {
            return new JsonResponse(['error' => 'Role not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (array_key_exists('name', $payload)) {
            $newName = $this->normalizeString($payload['name']);
            if ($newName === null) {
                return new JsonResponse(['error' => 'Invalid name field'], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($newName !== $role->getName() && $roleService->getRoleByName($newName)) {
                return new JsonResponse(['error' => 'Role name already exists'], JsonResponse::HTTP_CONFLICT);
            }

            $role->setName($newName);
        }

        if (array_key_exists('description', $payload)) {
            $role->setDescription($this->normalizeNullableString($payload['description']));
        }

        $roleService->saveRole($role);

        return new JsonResponse($this->serializeRole($role));
    }

    #[Route('/{id}', name: 'role_delete', methods: ['DELETE'])]
    public function delete(int $id, RoleService $roleService): JsonResponse
    {
        $role = $roleService->getRoleById($id);
        if (!$role) {
            return new JsonResponse(['error' => 'Role not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($role->getNpcs()->count() > 0) {
            return new JsonResponse(
                ['error' => 'Cannot delete role while NPCs are assigned to it'],
                JsonResponse::HTTP_CONFLICT
            );
        }

        $roleService->deleteRole($role);

        return new JsonResponse(['message' => 'Role deleted successfully']);
    }

    private function serializeRole(Role $role): array
    {
        return [
            'id' => $role->getId(),
            'name' => $role->getName(),
            'description' => $role->getDescription(),
        ];
    }

    private function normalizeString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
