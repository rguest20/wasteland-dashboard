<?php

namespace App\Service;

use App\Entity\WorldSecret;
use App\Repository\WorldSecretRepository;

class WorldSecretService
{
    public function __construct(
        private readonly WorldSecretRepository $worldSecretRepository,
    ) {
    }

    /**
     * @return WorldSecret[]
     */
    public function getAllWorldSecrets(): array
    {
        return $this->worldSecretRepository->findBy([], ['title' => 'ASC']);
    }

    public function getWorldSecretById(int $id): ?WorldSecret
    {
        return $this->worldSecretRepository->find($id);
    }

    public function getWorldSecretByTitle(string $title): ?WorldSecret
    {
        return $this->worldSecretRepository->findOneBy(['title' => $title]);
    }

    public function saveWorldSecret(WorldSecret $worldSecret): void
    {
        $this->worldSecretRepository->save($worldSecret);
    }

    public function deleteWorldSecret(WorldSecret $worldSecret): void
    {
        $this->worldSecretRepository->delete($worldSecret);
    }
}
