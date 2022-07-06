<?php

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'user';
    public const ADMIN = 'admin';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::ADMIN,
            self::USER
        ]);
        $this->name = $name;
    }

    public function isEqualTo(Role $role): bool
    {
        return $this->name === $role->getName();
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
