<?php

namespace App\Tests\Controller;

use App\Controller\LocationController;
use App\Entity\Location;
use App\Entity\Npc;
use App\Entity\Role;
use App\Repository\LocationRepository;
use App\Service\LocationService;
use App\Service\NpcService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class LocationControllerTest extends TestCase
{
    public function testListReturnsLocations(): void
    {
        $location = (new Location())
            ->setName('Shady Sands')
            ->setDefence(78)
            ->setFood(84)
            ->setMorale(72)
            ->setStanding(65);

        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$location]);

        $response = (new LocationController())->list(new LocationService($repository));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([[
            'id' => null,
            'name' => 'Shady Sands',
            'defence' => 78,
            'food' => 84,
            'morale' => 72,
            'standing' => 65,
        ]], $this->decodeJson($response));
    }

    public function testGetReturnsNotFoundWhenLocationDoesNotExist(): void
    {
        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(21)
            ->willReturn(null);

        $response = (new LocationController())->get(21, new LocationService($repository));

        self::assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertSame(['error' => 'Location not found'], $this->decodeJson($response));
    }

    public function testNpcsReturnsNotFoundWhenLocationDoesNotExist(): void
    {
        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(99)
            ->willReturn(null);

        $npcService = $this->createMock(NpcService::class);
        $npcService->expects(self::never())->method('getNpcsByLocation');

        $response = (new LocationController())->npcs(99, new LocationService($repository), $npcService);

        self::assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertSame(['error' => 'Location not found'], $this->decodeJson($response));
    }

    public function testNpcsReturnsSerializedNpcsForLocation(): void
    {
        $location = (new Location())
            ->setName('Shady Sands')
            ->setDefence(78)
            ->setFood(84)
            ->setMorale(72)
            ->setStanding(65);

        $role = (new Role())->setName('Merchant');
        $createdAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $npc = (new Npc())
            ->setId(5)
            ->setName('Trudy')
            ->setRole($role)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($createdAt);

        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(4)
            ->willReturn($location);

        $npcService = $this->createMock(NpcService::class);
        $npcService
            ->expects(self::once())
            ->method('getNpcsByLocation')
            ->with(self::identicalTo($location))
            ->willReturn([$npc]);

        $response = (new LocationController())->npcs(4, new LocationService($repository), $npcService);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([[
            'id' => 5,
            'name' => 'Trudy',
            'role' => 'Merchant',
            'created_at' => $createdAt->format(DATE_ATOM),
        ]], $this->decodeJson($response));
    }

    public function testCreateReturnsCreatedLocation(): void
    {
        $request = new Request(content: json_encode([
            'name' => 'Shady Sands',
            'defence' => 78,
            'food' => 84,
            'morale' => 72,
            'standing' => 65,
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Shady Sands'])
            ->willReturn(null);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function (Location $location): bool {
                return $location->getName() === 'Shady Sands'
                    && $location->getDefence() === 78
                    && $location->getFood() === 84
                    && $location->getMorale() === 72
                    && $location->getStanding() === 65;
            }));

        $response = (new LocationController())->create($request, new LocationService($repository));

        self::assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        self::assertSame([
            'id' => null,
            'name' => 'Shady Sands',
            'defence' => 78,
            'food' => 84,
            'morale' => 72,
            'standing' => 65,
        ], $this->decodeJson($response));
    }

    public function testCreateReturnsBadRequestWhenStatsMissing(): void
    {
        $request = new Request(content: json_encode([
            'name' => 'Shady Sands',
            'defence' => 78,
            'food' => 84,
            'morale' => 72,
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Shady Sands'])
            ->willReturn(null);
        $repository->expects(self::never())->method('save');

        $response = (new LocationController())->create($request, new LocationService($repository));

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(
            ['error' => 'Fields defence, food, morale, and standing are required integers'],
            $this->decodeJson($response)
        );
    }

    public function testUpdateReturnsUpdatedLocation(): void
    {
        $location = (new Location())
            ->setName('Shady Sands')
            ->setDefence(78)
            ->setFood(84)
            ->setMorale(72)
            ->setStanding(65);

        $request = new Request(content: json_encode([
            'name' => 'The Hub',
            'standing' => 70,
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(4)
            ->willReturn($location);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'The Hub'])
            ->willReturn(null);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($location));

        $response = (new LocationController())->update(4, $request, new LocationService($repository));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            'id' => null,
            'name' => 'The Hub',
            'defence' => 78,
            'food' => 84,
            'morale' => 72,
            'standing' => 70,
        ], $this->decodeJson($response));
    }

    public function testDeleteReturnsConflictWhenLocationHasNpcs(): void
    {
        $location = (new Location())
            ->setName('Shady Sands')
            ->setDefence(78)
            ->setFood(84)
            ->setMorale(72)
            ->setStanding(65);
        $location->addNpc(new Npc());

        $repository = $this->createMock(LocationRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(9)
            ->willReturn($location);
        $repository->expects(self::never())->method('delete');

        $response = (new LocationController())->delete(9, new LocationService($repository));

        self::assertSame(JsonResponse::HTTP_CONFLICT, $response->getStatusCode());
        self::assertSame(
            ['error' => 'Cannot delete location while NPCs are assigned to it'],
            $this->decodeJson($response)
        );
    }

    private function decodeJson(JsonResponse $response): array
    {
        $decoded = json_decode($response->getContent() ?: '', true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return $decoded;
    }
}
