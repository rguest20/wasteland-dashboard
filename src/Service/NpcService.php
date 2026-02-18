<?php

namespace App\Service;

use App\Dto\CreateNpcRequest;
use App\Entity\Npc;
use App\Entity\Role;
use App\Repository\NpcRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
final class NpcService
{
    public function __construct(
        private readonly NpcRepository $npcRepository,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function getAllNpcs(): array
    {
        return $this->npcRepository->findAllWithRole();
    }

    public function getNpcById(int $id): ?Npc
    {
        return $this->npcRepository->findOneWithRole($id);
    }

    public function deleteNpc(Npc $npc): void
    {
        $em = $this->npcRepository->getEntityManager();
        $em->remove($npc);
        $em->flush();
    }

    public function saveNpc(Npc $npc): void
    {
        $this->npcRepository->save($npc);
    }

    public function validateCreateNpcRequest(CreateNpcRequest $dto): array
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $out = [];
            foreach ($errors as $error) {
                $out[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $out;
        }
        return [];
    }

    public function createNpc(CreateNpcRequest $dto, ?Role $role): Npc
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $npc = new Npc();

        $npc->setName($dto->name);
        $npc->setNotes($dto->notes);
        $npc->setRole($role);
        $npc->setCreatedAt($now);
        $npc->setUpdatedAt($now);
        $this->npcRepository->save($npc);

        return $npc;
    }
}