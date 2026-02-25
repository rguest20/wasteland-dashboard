<?php

namespace App\Tests\Controller;

use App\Controller\NpcController;
use App\Entity\Location;
use App\Entity\Knowledge;
use App\Entity\Npc;
use App\Entity\NpcSkill;
use App\Entity\Role;
use App\Entity\Skill;
use App\Entity\WorldSecret;
use App\Repository\LocationRepository;
use App\Repository\NpcRepository;
use App\Repository\RoleRepository;
use App\Service\LocationService;
use App\Service\NpcService;
use App\Service\RoleService;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class NpcControllerTest extends TestCase
{
    public function testListReturnsSerializedNpcs(): void
    {
        $role = (new Role())->setName('Merchant');
        $createdAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $npc = (new Npc())
            ->setId(10)
            ->setName('Trudy')
            ->setNotes('Runs the caravan')
            ->setRole($role)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($createdAt);

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository
            ->expects(self::once())
            ->method('findAllWithRelations')
            ->willReturn([$npc]);

        $npcService = new NpcService(
            $npcRepository,
            Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
        );

        $response = $this->createController()->list($npcService);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            [
                'id' => 10,
                'name' => 'Trudy',
                'notes' => 'Runs the caravan',
                'role' => 'Merchant',
                'role_id' => null,
                'location' => null,
                'location_id' => null,
                'strength' => 5,
                'perception' => 5,
                'endurance' => 5,
                'charisma' => 5,
                'intelligence' => 5,
                'agility' => 5,
                'luck' => 5,
                'skills' => [],
                'knowledge' => [],
                'created_at' => $createdAt->format(DATE_ATOM),
            ],
        ], $this->decodeJson($response));
    }

    public function testGetReturnsNpcWhenFound(): void
    {
        $role = (new Role())->setName('Merchant');
        $createdAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-01-02T10:00:00+00:00');

        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setNotes('Runs the caravan')
            ->setRole($role)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt);

        $response = $this->createController()->get(
            $npc
        );

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            'id' => 12,
            'name' => 'Trudy',
            'notes' => 'Runs the caravan',
            'role' => 'Merchant',
            'role_id' => null,
            'location' => null,
            'location_id' => null,
            'strength' => 5,
            'perception' => 5,
            'endurance' => 5,
            'charisma' => 5,
            'intelligence' => 5,
            'agility' => 5,
            'luck' => 5,
            'skills' => [],
            'knowledge' => [],
            'created_at' => $createdAt->format(DATE_ATOM),
            'updated_at' => $updatedAt->format(DATE_ATOM),
        ], $this->decodeJson($response));
    }

    public function testGetReturnsNpcWithLocationWhenPresent(): void
    {
        $role = (new Role())->setName('Merchant');
        $location = (new Location())->setName('Shady Sands');
        $createdAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-01-02T10:00:00+00:00');

        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setNotes('Runs the caravan')
            ->setRole($role)
            ->setLocation($location)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt);

        $response = $this->createController()->get(
            $npc
        );

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            'id' => 12,
            'name' => 'Trudy',
            'notes' => 'Runs the caravan',
            'role' => 'Merchant',
            'role_id' => null,
            'location' => 'Shady Sands',
            'location_id' => null,
            'strength' => 5,
            'perception' => 5,
            'endurance' => 5,
            'charisma' => 5,
            'intelligence' => 5,
            'agility' => 5,
            'luck' => 5,
            'skills' => [],
            'knowledge' => [],
            'created_at' => $createdAt->format(DATE_ATOM),
            'updated_at' => $updatedAt->format(DATE_ATOM),
        ], $this->decodeJson($response));
    }

    public function testGetReturnsNpcWithSkillsWhenPresent(): void
    {
        $createdAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-01-02T10:00:00+00:00');

        $speech = (new Skill())->setName('Speech');
        $barter = (new Skill())->setName('Barter');

        $npc = (new Npc())
            ->setId(77)
            ->setName('Moira Brown')
            ->setNotes('Wasteland guide')
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt);

        $npcSkillOne = (new NpcSkill())
            ->setNpcId($npc)
            ->setSkillId($speech)
            ->setLevel(6);
        $npcSkillTwo = (new NpcSkill())
            ->setNpcId($npc)
            ->setSkillId($barter)
            ->setLevel(5);

        $npc->addNpcSkill($npcSkillOne);
        $npc->addNpcSkill($npcSkillTwo);

        $response = $this->createController()->get($npc);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $data = $this->decodeJson($response);
        self::assertSame(77, $data['id']);
        self::assertSame('Moira Brown', $data['name']);
        self::assertCount(2, $data['skills']);
        self::assertSame('Speech', $data['skills'][0]['name']);
        self::assertSame(6, $data['skills'][0]['level']);
        self::assertSame('Barter', $data['skills'][1]['name']);
        self::assertSame(5, $data['skills'][1]['level']);
        self::assertSame([], $data['knowledge']);
    }

    public function testGetReturnsNpcWithKnowledgeWhenPresent(): void
    {
        $createdAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2026-01-02T10:00:00+00:00');

        $npc = (new Npc())
            ->setId(88)
            ->setName('Ezekiel')
            ->setNotes('Radio host')
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt);

        $knowledgeOne = (new Knowledge())
            ->setTitle('Brotherhood Frequencies')
            ->setDescription('Knows old BOS signal patterns.')
            ->setCategory('Military Intel');
        $knowledgeTwo = (new Knowledge())
            ->setTitle('Market Gossip')
            ->setDescription('Tracks caravan rumors and prices.')
            ->setCategory('Trade');

        $worldSecret = (new WorldSecret())
            ->setTitle('Project Purity Logs')
            ->setDescription('Hidden Enclave notes.')
            ->setCategory('Enclave');
        $knowledgeOne->setWorldSecret($worldSecret);

        $npc->addKnowledge($knowledgeOne);
        $npc->addKnowledge($knowledgeTwo);

        $response = $this->createController()->get($npc);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        $data = $this->decodeJson($response);
        self::assertCount(2, $data['knowledge']);
        self::assertSame('Brotherhood Frequencies', $data['knowledge'][0]['title']);
        self::assertSame('Military Intel', $data['knowledge'][0]['category']);
        self::assertNull($data['knowledge'][0]['world_secret_id']);
        self::assertSame('Project Purity Logs', $data['knowledge'][0]['world_secret_title']);
        self::assertSame('Market Gossip', $data['knowledge'][1]['title']);
        self::assertSame('Trade', $data['knowledge'][1]['category']);
        self::assertNull($data['knowledge'][1]['world_secret_id']);
        self::assertNull($data['knowledge'][1]['world_secret_title']);
    }

    public function testCreateReturnsBadRequestForInvalidJson(): void
    {
        $request = new Request(content: 'not-json');

        $response = $this->createController()->create(
            $request,
            new NpcService(
                $this->createStub(NpcRepository::class),
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($this->createStub(RoleRepository::class))
        );

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(['error' => 'Invalid JSON'], $this->decodeJson($response));
    }

    public function testCreateReturnsUnprocessableEntityWhenValidationFails(): void
    {
        $request = new Request(content: json_encode([
            'name' => '',
            'notes' => 'Runs the caravan',
            'role_name' => 'Merchant',
        ], JSON_THROW_ON_ERROR));

        $roleRepository = $this->createMock(RoleRepository::class);
        $roleRepository->expects(self::never())->method('findOneBy');

        $response = $this->createController()->create(
            $request,
            new NpcService(
                $this->createStub(NpcRepository::class),
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($roleRepository)
        );

        self::assertSame(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['name' => ['This value should not be blank.']]],
            $this->decodeJson($response)
        );
    }

    public function testCreateReturnsCreatedNpc(): void
    {
        $request = new Request(content: json_encode([
            'name' => 'Trudy',
            'notes' => 'Runs the caravan',
            'role_name' => 'Merchant',
        ], JSON_THROW_ON_ERROR));

        $role = (new Role())->setName('Merchant');

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function (Npc $npc): bool {
                $npc->setId(99);

                return $npc->getName() === 'Trudy'
                    && $npc->getNotes() === 'Runs the caravan'
                    && $npc->getRole()?->getName() === 'Merchant';
            }));

        $roleRepository = $this->createMock(RoleRepository::class);
        $roleRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Merchant'])
            ->willReturn($role);

        $response = $this->createController()->create(
            $request,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($roleRepository)
        );

        $data = $this->decodeJson($response);

        self::assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        self::assertSame(99, $data['id']);
        self::assertSame('Trudy', $data['name']);
        self::assertSame('Runs the caravan', $data['notes']);
        self::assertSame('Merchant', $data['role']);
        self::assertNull($data['role_id']);
        self::assertNull($data['location']);
        self::assertNull($data['location_id']);
        self::assertMatchesRegularExpression('/^\\d{4}-\\d{2}-\\d{2}T/', $data['created_at']);
        self::assertSame($data['created_at'], $data['updated_at']);
    }

    public function testUpdateReturnsBadRequestWhenNotesAreMissing(): void
    {
        $request = new Request(content: json_encode([
            'name' => 'Updated name',
        ], JSON_THROW_ON_ERROR));
        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $response = $this->createController()->update(
            $npc,
            $request,
            new NpcService(
                $this->createStub(NpcRepository::class),
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($this->createStub(RoleRepository::class))
        );

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(['error' => 'Missing notes field'], $this->decodeJson($response));
    }

    public function testUpdateReturnsBadRequestForInvalidJson(): void
    {
        $request = new Request(content: '{bad json');
        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $response = $this->createController()->update(
            $npc,
            $request,
            new NpcService(
                $this->createStub(NpcRepository::class),
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($this->createStub(RoleRepository::class))
        );

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(['error' => 'Invalid JSON'], $this->decodeJson($response));
    }

    public function testUpdateReturnsUpdatedNpc(): void
    {
        $oldUpdatedAt = new \DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $createdAt = new \DateTimeImmutable('2025-12-31T10:00:00+00:00');

        $oldRole = (new Role())->setName('Merchant');
        $newRole = (new Role())->setName('Raider');

        $npc = (new Npc())
            ->setId(15)
            ->setName('Trudy')
            ->setNotes('Old notes')
            ->setRole($oldRole)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($oldUpdatedAt);

        $request = new Request(content: json_encode([
            'name' => 'Trudy Updated',
            'notes' => 'New notes',
            'role_name' => 'Raider',
        ], JSON_THROW_ON_ERROR));

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($npc));

        $roleRepository = $this->createMock(RoleRepository::class);
        $roleRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Raider'])
            ->willReturn($newRole);

        $response = $this->createController()->update(
            $npc,
            $request,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($roleRepository)
        );

        $data = $this->decodeJson($response);

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame('Trudy Updated', $data['name']);
        self::assertSame('New notes', $data['notes']);
        self::assertSame('Raider', $data['role']);
        self::assertNull($data['role_id']);
        self::assertNull($data['location']);
        self::assertNull($data['location_id']);
        self::assertSame($createdAt->format(DATE_ATOM), $data['created_at']);
        self::assertNotSame($oldUpdatedAt->format(DATE_ATOM), $data['updated_at']);
    }

    public function testUpdateReturnsUnprocessableEntityWhenRoleDoesNotExist(): void
    {
        $npc = (new Npc())
            ->setId(22)
            ->setName('Trudy')
            ->setNotes('Old notes')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $request = new Request(content: json_encode([
            'notes' => 'New notes',
            'role_name' => 'Nope',
        ], JSON_THROW_ON_ERROR));

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository->expects(self::never())->method('save');

        $roleRepository = $this->createMock(RoleRepository::class);
        $roleRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['name' => 'Nope'])
            ->willReturn(null);

        $response = $this->createController()->update(
            $npc,
            $request,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new RoleService($roleRepository)
        );

        self::assertSame(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        self::assertSame(
            ['errors' => ['role_name' => ['Unknown role_name']]],
            $this->decodeJson($response)
        );
    }

    public function testMoveReturnsBadRequestWhenLocationIdMissing(): void
    {
        $request = new Request(content: json_encode([], JSON_THROW_ON_ERROR));
        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository->expects(self::never())->method('save');

        $response = $this->createController()->move(
            $npc,
            $request,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new LocationService($this->createStub(LocationRepository::class))
        );

        self::assertSame(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertSame(['error' => 'Missing location_id field'], $this->decodeJson($response));
    }

    public function testMoveReturnsUnprocessableEntityWhenLocationDoesNotExist(): void
    {
        $request = new Request(content: json_encode(['location_id' => 999], JSON_THROW_ON_ERROR));
        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository->expects(self::never())->method('save');

        $locationRepository = $this->createMock(LocationRepository::class);
        $locationRepository
            ->expects(self::once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $response = $this->createController()->move(
            $npc,
            $request,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new LocationService($locationRepository)
        );

        self::assertSame(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        self::assertSame(['error' => 'Location not found'], $this->decodeJson($response));
    }

    public function testMoveUpdatesNpcLocation(): void
    {
        $location = (new Location())->setName('Shady Sands');
        $npc = (new Npc())
            ->setId(12)
            ->setName('Trudy')
            ->setCreatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'))
            ->setUpdatedAt(new \DateTimeImmutable('2026-01-01T10:00:00+00:00'));

        $request = new Request(content: json_encode(['location_id' => 5], JSON_THROW_ON_ERROR));

        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($npc));

        $locationRepository = $this->createMock(LocationRepository::class);
        $locationRepository
            ->expects(self::once())
            ->method('find')
            ->with(5)
            ->willReturn($location);

        $response = $this->createController()->move(
            $npc,
            $request,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            ),
            new LocationService($locationRepository)
        );

        $data = $this->decodeJson($response);
        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame(12, $data['id']);
        self::assertSame('Shady Sands', $data['location']);
        self::assertArrayHasKey('updated_at', $data);
    }

    public function testDeleteReturnsNotFoundWhenNpcDoesNotExist(): void
    {
        $npcRepository = $this->createMock(NpcRepository::class);
        $npcRepository
            ->expects(self::once())
            ->method('findOneWithRelations')
            ->with(8)
            ->willReturn(null);

        $response = $this->createController()->delete(
            8,
            new NpcService(
                $npcRepository,
                Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator()
            )
        );

        self::assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertSame(['error' => 'NPC not found'], $this->decodeJson($response));
    }

    private function decodeJson(JsonResponse $response): array
    {
        $decoded = json_decode($response->getContent() ?: '', true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return $decoded;
    }

    private function createController(): NpcController
    {
        $controller = new NpcController();
        $container = $this->createStub(ContainerInterface::class);
        $container->method('has')->willReturn(false);
        $controller->setContainer($container);

        return $controller;
    }
}
