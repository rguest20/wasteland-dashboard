<?php

namespace App\Controller;

use App\Entity\Knowledge;
use App\Entity\WorldSecret;
use App\Service\WorldSecretService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/worldsecrets')]
final class WorldSecretController
{
    #[Route('', name: 'world_secret_list', methods: ['GET'])]
    public function list(WorldSecretService $worldSecretService): JsonResponse
    {
        $worldSecrets = array_map(
            fn (WorldSecret $worldSecret) => $this->serializeWorldSecret($worldSecret),
            $worldSecretService->getAllWorldSecrets()
        );

        return new JsonResponse($worldSecrets);
    }

    #[Route('/{id}', name: 'world_secret_get', methods: ['GET'])]
    public function get(int $id, WorldSecretService $worldSecretService): JsonResponse
    {
        $worldSecret = $worldSecretService->getWorldSecretById($id);
        if (!$worldSecret) {
            return new JsonResponse(['error' => 'World secret not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->serializeWorldSecret($worldSecret, true));
    }

    #[Route('', name: 'world_secret_create', methods: ['POST'])]
    public function create(Request $request, WorldSecretService $worldSecretService): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $title = $this->normalizeString($payload['title'] ?? null);
        if ($title === null) {
            return new JsonResponse(['error' => 'Missing title field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($worldSecretService->getWorldSecretByTitle($title)) {
            return new JsonResponse(['error' => 'World secret title already exists'], JsonResponse::HTTP_CONFLICT);
        }

        $description = $this->normalizeString($payload['description'] ?? null);
        if ($description === null) {
            return new JsonResponse(['error' => 'Missing description field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $worldSecret = new WorldSecret();
        $worldSecret->setTitle($title);
        $worldSecret->setDescription($description);
        $worldSecret->setCategory($this->normalizeNullableString($payload['category'] ?? null));

        $worldSecretService->saveWorldSecret($worldSecret);

        return new JsonResponse($this->serializeWorldSecret($worldSecret), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'world_secret_update', methods: ['PUT'])]
    public function update(int $id, Request $request, WorldSecretService $worldSecretService): JsonResponse
    {
        $worldSecret = $worldSecretService->getWorldSecretById($id);
        if (!$worldSecret) {
            return new JsonResponse(['error' => 'World secret not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (array_key_exists('title', $payload)) {
            $title = $this->normalizeString($payload['title']);
            if ($title === null) {
                return new JsonResponse(['error' => 'Invalid title field'], JsonResponse::HTTP_BAD_REQUEST);
            }

            if ($title !== $worldSecret->getTitle() && $worldSecretService->getWorldSecretByTitle($title)) {
                return new JsonResponse(['error' => 'World secret title already exists'], JsonResponse::HTTP_CONFLICT);
            }

            $worldSecret->setTitle($title);
        }

        if (array_key_exists('description', $payload)) {
            $description = $this->normalizeString($payload['description']);
            if ($description === null) {
                return new JsonResponse(['error' => 'Invalid description field'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $worldSecret->setDescription($description);
        }

        if (array_key_exists('category', $payload)) {
            $worldSecret->setCategory($this->normalizeNullableString($payload['category']));
        }

        $worldSecretService->saveWorldSecret($worldSecret);

        return new JsonResponse($this->serializeWorldSecret($worldSecret, true));
    }

    #[Route('/{id}', name: 'world_secret_delete', methods: ['DELETE'])]
    public function delete(int $id, WorldSecretService $worldSecretService): JsonResponse
    {
        $worldSecret = $worldSecretService->getWorldSecretById($id);
        if (!$worldSecret) {
            return new JsonResponse(['error' => 'World secret not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($worldSecret->getKnowledge()->count() > 0) {
            return new JsonResponse(
                ['error' => 'Cannot delete world secret while knowledge is assigned to it'],
                JsonResponse::HTTP_CONFLICT
            );
        }

        $worldSecretService->deleteWorldSecret($worldSecret);

        return new JsonResponse(['message' => 'World secret deleted successfully']);
    }

    private function serializeWorldSecret(WorldSecret $worldSecret, bool $includeKnowledge = false): array
    {
        $payload = [
            'id' => $worldSecret->getId(),
            'name' => $worldSecret->getTitle(),
            'title' => $worldSecret->getTitle(),
            'description' => $worldSecret->getDescription(),
            'category' => $worldSecret->getCategory(),
            'knowledge_count' => $worldSecret->getKnowledge()->count(),
        ];

        if ($includeKnowledge) {
            $payload['knowledge'] = array_map(
                static fn (Knowledge $knowledge) => [
                    'id' => $knowledge->getId(),
                    'title' => $knowledge->getTitle(),
                    'category' => $knowledge->getCategory(),
                    'npc_id' => $knowledge->getNpc()?->getId(),
                    'npc_name' => $knowledge->getNpc()?->getName(),
                ],
                $worldSecret->getKnowledge()->toArray()
            );
        }

        return $payload;
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
