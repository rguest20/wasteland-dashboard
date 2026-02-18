<?php

namespace App\Service;

use App\Entity\Role;
use App\Repository\RoleRepository;

final class RoleService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function getRoleByName(string $name): ?Role
    {
        return $this->roleRepository->findOneBy(['name' => $name]);
    }
}