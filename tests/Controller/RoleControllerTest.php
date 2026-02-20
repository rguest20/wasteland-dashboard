<?php

namespace App\Tests\Controller;

use App\Controller\RoleController;
use App\Entity\Npc;
use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Service\RoleService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class RoleControllerTest extends TestCase
{
    public function testListReturnsRoles(): void
    {
        $role = (new Role())
            ->setName('Merchant')
            ->setDescription('Trading guild');

        $repository = $this->createMock(RoleRepository::class);
        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$role]);

        $response = (new RoleController())->list(new RoleService($repository));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([[
            'id' => null,
            'name' => 'Merchant',
            'description' => 'Trading guild',
        ]], $this->decodeJson($response));
    }

    public function testGetReturnsNotFoundWhenRoleDoesNotExist(): void
    {
        $repository = $this->createMock(RoleRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(21)
            ->willReturn(null);

        $response = (new RoleController())->get(21, new RoleService($repository));

        self::assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertSame(['error' => 'Role not found'], $this->decodeJson($response));
    }

    public function testCreateReturnsCreatedRole(): void
    {
        $request = new Request(content: json_encode([
            'name' => 'Merchant',
            'description' => 'Trading guild',
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(RoleRepository::class);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Merchant'])
            ->willReturn(null);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function (Role $role): bool {
                return $role->getName() === 'Merchant'
                    && $role->getDescription() === 'Trading guild';
            }));

        $response = (new RoleController())->create($request, new RoleService($repository));

        self::assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        self::assertSame([
            'id' => null,
            'name' => 'Merchant',
            'description' => 'Trading guild',
        ], $this->decodeJson($response));
    }

    public function testCreateReturnsConflictWhenRoleNameExists(): void
    {
        $request = new Request(content: json_encode([
            'name' => 'Merchant',
        ], JSON_THROW_ON_ERROR));

        $existing = (new Role())->setName('Merchant');

        $repository = $this->createMock(RoleRepository::class);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Merchant'])
            ->willReturn($existing);
        $repository->expects(self::never())->method('save');

        $response = (new RoleController())->create($request, new RoleService($repository));

        self::assertSame(JsonResponse::HTTP_CONFLICT, $response->getStatusCode());
        self::assertSame(['error' => 'Role name already exists'], $this->decodeJson($response));
    }

    public function testUpdateReturnsUpdatedRole(): void
    {
        $existing = (new Role())
            ->setName('Merchant')
            ->setDescription('Old');

        $request = new Request(content: json_encode([
            'name' => 'Trader',
            'description' => 'Updated',
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(RoleRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(7)
            ->willReturn($existing);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Trader'])
            ->willReturn(null);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($existing));

        $response = (new RoleController())->update(7, $request, new RoleService($repository));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            'id' => null,
            'name' => 'Trader',
            'description' => 'Updated',
        ], $this->decodeJson($response));
    }

    public function testDeleteReturnsConflictWhenRoleHasNpcs(): void
    {
        $role = (new Role())->setName('Merchant');
        $role->addNpc(new Npc());

        $repository = $this->createMock(RoleRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(9)
            ->willReturn($role);
        $repository->expects(self::never())->method('delete');

        $response = (new RoleController())->delete(9, new RoleService($repository));

        self::assertSame(JsonResponse::HTTP_CONFLICT, $response->getStatusCode());
        self::assertSame(
            ['error' => 'Cannot delete role while NPCs are assigned to it'],
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
