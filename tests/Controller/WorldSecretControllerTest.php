<?php

namespace App\Tests\Controller;

use App\Controller\WorldSecretController;
use App\Entity\Knowledge;
use App\Entity\WorldSecret;
use App\Repository\WorldSecretRepository;
use App\Service\WorldSecretService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class WorldSecretControllerTest extends TestCase
{
    public function testListReturnsWorldSecrets(): void
    {
        $secret = (new WorldSecret())
            ->setTitle('Vault-Tec Blacksite')
            ->setDescription('A hidden pre-war vault map.')
            ->setCategory('Pre-War');

        $repository = $this->createMock(WorldSecretRepository::class);
        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with([], ['title' => 'ASC'])
            ->willReturn([$secret]);

        $response = (new WorldSecretController())->list(new WorldSecretService($repository));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([[
            'id' => null,
            'name' => 'Vault-Tec Blacksite',
            'title' => 'Vault-Tec Blacksite',
            'description' => 'A hidden pre-war vault map.',
            'category' => 'Pre-War',
            'knowledge_count' => 0,
        ]], $this->decodeJson($response));
    }

    public function testGetReturnsNotFoundWhenWorldSecretMissing(): void
    {
        $repository = $this->createMock(WorldSecretRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(41)
            ->willReturn(null);

        $response = (new WorldSecretController())->get(41, new WorldSecretService($repository));

        self::assertSame(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertSame(['error' => 'World secret not found'], $this->decodeJson($response));
    }

    public function testCreateReturnsCreatedWorldSecret(): void
    {
        $request = new Request(content: json_encode([
            'title' => 'Institute Terminal Cache',
            'description' => 'Coordinates to hidden relay nodes.',
            'category' => 'Institute',
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(WorldSecretRepository::class);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['title' => 'Institute Terminal Cache'])
            ->willReturn(null);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::callback(function (WorldSecret $secret): bool {
                return $secret->getTitle() === 'Institute Terminal Cache'
                    && $secret->getDescription() === 'Coordinates to hidden relay nodes.'
                    && $secret->getCategory() === 'Institute';
            }));

        $response = (new WorldSecretController())->create($request, new WorldSecretService($repository));

        self::assertSame(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        self::assertSame([
            'id' => null,
            'name' => 'Institute Terminal Cache',
            'title' => 'Institute Terminal Cache',
            'description' => 'Coordinates to hidden relay nodes.',
            'category' => 'Institute',
            'knowledge_count' => 0,
        ], $this->decodeJson($response));
    }

    public function testUpdateReturnsUpdatedWorldSecret(): void
    {
        $secret = (new WorldSecret())
            ->setTitle('Old Name')
            ->setDescription('Old description')
            ->setCategory('Old');

        $request = new Request(content: json_encode([
            'title' => 'New Name',
            'description' => 'New description',
            'category' => 'New',
        ], JSON_THROW_ON_ERROR));

        $repository = $this->createMock(WorldSecretRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(7)
            ->willReturn($secret);
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['title' => 'New Name'])
            ->willReturn(null);
        $repository
            ->expects(self::once())
            ->method('save')
            ->with(self::identicalTo($secret));

        $response = (new WorldSecretController())->update(7, $request, new WorldSecretService($repository));

        self::assertSame(JsonResponse::HTTP_OK, $response->getStatusCode());
        self::assertSame([
            'id' => null,
            'name' => 'New Name',
            'title' => 'New Name',
            'description' => 'New description',
            'category' => 'New',
            'knowledge_count' => 0,
            'knowledge' => [],
        ], $this->decodeJson($response));
    }

    public function testDeleteReturnsConflictWhenWorldSecretHasKnowledge(): void
    {
        $secret = (new WorldSecret())
            ->setTitle('Vault Key')
            ->setDescription('Key route')
            ->setCategory('Pre-War');
        $secret->addKnowledge((new Knowledge())->setTitle('Route')->setDescription('Route data')->setCategory('Ops'));

        $repository = $this->createMock(WorldSecretRepository::class);
        $repository
            ->expects(self::once())
            ->method('find')
            ->with(8)
            ->willReturn($secret);
        $repository->expects(self::never())->method('delete');

        $response = (new WorldSecretController())->delete(8, new WorldSecretService($repository));

        self::assertSame(JsonResponse::HTTP_CONFLICT, $response->getStatusCode());
        self::assertSame(
            ['error' => 'Cannot delete world secret while knowledge is assigned to it'],
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
