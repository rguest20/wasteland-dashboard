<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Npc;
use App\Service\LocationService;
use App\Service\NpcService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/locations')]
final class LocationController
{
    #[Route('', name: 'location_list', methods: ['GET'])]
    public function list(LocationService $locationService): JsonResponse
    {
        $locations = array_map(
            fn (Location $location) => $this->serializeLocation($location),
            $locationService->getAllLocations()
        );

        return new JsonResponse($locations);
    }

    #[Route('/{id}', name: 'location_get', methods: ['GET'])]
    public function get(int $id, LocationService $locationService): JsonResponse
    {
        $location = $locationService->getLocationById($id);
        if (!$location) {
            return new JsonResponse(['error' => 'Location not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->serializeLocation($location));
    }

    #[Route('/{id}/npcs', name: 'location_npcs', methods: ['GET'])]
    public function npcs(int $id, LocationService $locationService, NpcService $npcService): JsonResponse
    {
        $location = $locationService->getLocationById($id);
        if (!$location) {
            return new JsonResponse(['error' => 'Location not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $npcs = $npcService->getNpcsByLocation($location);

        $result = array_map(static fn(Npc $npc) => [
            'id' => $npc->getId(),
            'name' => $npc->getName(),
            'role' => $npc->getRole()?->getName(),
            'created_at' => $npc->getCreatedAt()?->format(DATE_ATOM),
        ], $npcs);

        return new JsonResponse($result);
    }

    #[Route('', name: 'location_create', methods: ['POST'])]
    public function create(Request $request, LocationService $locationService): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = $this->normalizeString($payload['name'] ?? null);
        if ($name === null) {
            return new JsonResponse(['error' => 'Missing name field'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($locationService->getLocationByName($name)) {
            return new JsonResponse(['error' => 'Location name already exists'], JsonResponse::HTTP_CONFLICT);
        }

        $stats = $this->extractStats($payload);
        if ($stats === null) {
            return new JsonResponse(
                ['error' => 'Fields defence, food, morale, and standing are required integers'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $location = new Location();
        $location->setName($name);
        $location->setDefence($stats['defence']);
        $location->setFood($stats['food']);
        $location->setMorale($stats['morale']);
        $location->setStanding($stats['standing']);

        $locationService->saveLocation($location);

        return new JsonResponse($this->serializeLocation($location), JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'location_update', methods: ['PUT'])]
    public function update(int $id, Request $request, LocationService $locationService): JsonResponse
    {
        $location = $locationService->getLocationById($id);
        if (!$location) {
            return new JsonResponse(['error' => 'Location not found'], JsonResponse::HTTP_NOT_FOUND);
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

            if ($newName !== $location->getName() && $locationService->getLocationByName($newName)) {
                return new JsonResponse(['error' => 'Location name already exists'], JsonResponse::HTTP_CONFLICT);
            }

            $location->setName($newName);
        }

        $numericKeys = ['defence', 'food', 'morale', 'standing'];
        foreach ($numericKeys as $key) {
            if (!array_key_exists($key, $payload)) {
                continue;
            }

            if (!is_int($payload[$key])) {
                return new JsonResponse(['error' => sprintf('Invalid %s field', $key)], JsonResponse::HTTP_BAD_REQUEST);
            }

            match ($key) {
                'defence' => $location->setDefence($payload[$key]),
                'food' => $location->setFood($payload[$key]),
                'morale' => $location->setMorale($payload[$key]),
                'standing' => $location->setStanding($payload[$key]),
            };
        }

        $locationService->saveLocation($location);

        return new JsonResponse($this->serializeLocation($location));
    }

    #[Route('/{id}', name: 'location_delete', methods: ['DELETE'])]
    public function delete(int $id, LocationService $locationService): JsonResponse
    {
        $location = $locationService->getLocationById($id);
        if (!$location) {
            return new JsonResponse(['error' => 'Location not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($location->getNpcs()->count() > 0) {
            return new JsonResponse(
                ['error' => 'Cannot delete location while NPCs are assigned to it'],
                JsonResponse::HTTP_CONFLICT
            );
        }

        $locationService->deleteLocation($location);

        return new JsonResponse(['message' => 'Location deleted successfully']);
    }

    private function serializeLocation(Location $location): array
    {
        return [
            'id' => $location->getId(),
            'name' => $location->getName(),
            'defence' => $location->getDefence(),
            'food' => $location->getFood(),
            'morale' => $location->getMorale(),
            'standing' => $location->getStanding(),
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

    private function extractStats(array $payload): ?array
    {
        $keys = ['defence', 'food', 'morale', 'standing'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $payload) || !is_int($payload[$key])) {
                return null;
            }
        }

        return [
            'defence' => $payload['defence'],
            'food' => $payload['food'],
            'morale' => $payload['morale'],
            'standing' => $payload['standing'],
        ];
    }
}
