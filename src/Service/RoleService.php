<?php

namespace App\Service;

use App\Entity\Role;
use App\Repository\RoleRepository;

class RoleService
{
    public function __construct(
        private readonly RoleRepository $roleRepository,
    ) {
    }

    public function getRoleByName(string $name): ?Role
    {
        return $this->roleRepository->findOneBy(['name' => $name]);
    }

    /**
     * @return Role[]
     */
    public function getAllRoles(): array
    {
        return $this->roleRepository->findBy([], ['name' => 'ASC']);
    }

    public function getRoleById(int $id): ?Role
    {
        return $this->roleRepository->find($id);
    }

    public function saveRole(Role $role): void
    {
        $this->roleRepository->save($role);
    }

    public function deleteRole(Role $role): void
    {
        $this->roleRepository->delete($role);
    }
}
