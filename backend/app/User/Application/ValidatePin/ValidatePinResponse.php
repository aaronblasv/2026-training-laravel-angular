<?php

declare(strict_types=1);

namespace App\User\Application\ValidatePin;

use App\User\Domain\Entity\User;

final readonly class ValidatePinResponse
{
    private function __construct(
        public string $uuid,
        public string $id,
        public string $name,
        public string $role,
        public ?string $imageSrc,
    ) {}

    public static function create(User $user): self
    {
        return new self(
            uuid: $user->uuid()->getValue(),
            id: $user->uuid()->getValue(),
            name: $user->name()->getValue(),
            role: $user->role()->getValue(),
            imageSrc: $user->imageSrc(),
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'image_src' => $this->imageSrc,
        ];
    }
}