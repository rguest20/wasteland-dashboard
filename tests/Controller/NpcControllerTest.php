<?php

namespace App\Tests\Controller;

use App\Controller\NpcController;
use App\Dto\CreateNpcRequest;
use App\Entity\Npc;
use App\Entity\Role;
use App\Service\NpcService;
use App\Service\RoleService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NpcControllerTest extends TestCase
{
    private NpcController $controller;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new NpcController();
    }

    public function testListReturnsArrayOfNpcs(): void
    {
        $role = (new Role())->setName('Merchant');

        $npc = new Npc();
        $npc->setName('Trudy');
        $npc->setNotes('Runs the caravan');
        $npc->setRole($role);
        $npc->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $npcService = $this->createStub(NpcService::class);
        $npcService->method('getAllNpcs')->willReturn([$npc]);

        $controller = $this->controller;

        $response = $controller->list($npcService);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame([
            [
                'id' => $npc->getId(), // may be null in pure unit test; that’s ok
                'name' => 'Trudy',
                'notes' => 'Runs the caravan',
                'role' => 'Merchant',
                'created_at' => '2026-01-01T10:00:00+00:00',
            ],
        ], $this->decode($response));
    }

    public function testGetReturns404WhenNpcMissing(): void
    {
        $npcService = $this->createMock(NpcService::class);
        $npcService->expects(self::once())->method('getNpcById')->with(123)->willReturn(null);

        $controller = $this->controller;

        $response = $controller->get($npcService, 123);

        self::assertSame(404, $response->getStatusCode());
        self::assertSame(['error' => 'NPC not found'], $this->decode($response));
    }

    public function testCreateReturns400OnInvalidJson(): void
    {
        $request = new Request(content: 'not-json');

        $controller = $this->controller;

        $response = $controller->create(
            $request,
            $this->createStub(NpcService::class),
            $this->createStub(RoleService::class),
        );

        self::assertSame(400, $response->getStatusCode());
        self::assertSame(['error' => 'Invalid JSON'], $this->decode($response));
    }

    public function testCreateReturns422OnUnknownRole(): void
    {
        $request = new Request(
            content: json_encode(['name' => 'Trudy', 'notes' => null, 'role_name' => 'Nope'], JSON_THROW_ON_ERROR)
        );

        $npcService = $this->createStub(NpcService::class);
        $npcService->method('validateCreateNpcRequest')->willReturn([]); // no validation errors

        $roleService = $this->createMock(RoleService::class);
        $roleService->expects(self::once())->method('getRoleByName')->with('Nope')->willReturn(null);

        $controller = $this->controller;

        $response = $controller->create($request, $npcService, $roleService);

        self::assertSame(422, $response->getStatusCode());
        self::assertSame(['errors' => ['role_name' => ['Unknown role_name']]], $this->decode($response));
    }

    public function testDeleteReturns200WhenDeleted(): void
    {
        $npc = new Npc();
        $npc->setName('Trudy');

        $npcService = $this->createMock(NpcService::class);
        $npcService->expects(self::once())->method('getNpcById')->with(7)->willReturn($npc);
        $npcService->expects(self::once())->method('deleteNpc')->with($npc);

        $controller = $this->controller;

        $response = $controller->delete(7, $npcService);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['message' => 'NPC deleted successfully'], $this->decode($response));
    }

    private function decode(JsonResponse $response): array
    {
        return json_decode($response->getContent() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
    }
}