<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

#[AllowDynamicProperties]
final class CreateNpcRequest
{
    const REQUIRED_FIELDS = ['name', 'notes', 'role_name'];

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $name = null;

    #[Assert\Length(max: 5000)]
    public ?string $notes = null;

    // Optional: if provided, must be non-empty
    #[Assert\Length(max: 100)]
    public ?string $role_name = null;

    public function loadData(array $data): void
    {
        if (!is_array($data)) {
            return;
        }
        $dataKeys = array_keys($data);
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!in_array($field, $dataKeys)) {
                throw new \InvalidArgumentException(sprintf('Missing required field: %s', $field));
            }
        }

        foreach (self::REQUIRED_FIELDS as $field) {
            $this->$field = $data[$field] ?? null;
        }
    }
}