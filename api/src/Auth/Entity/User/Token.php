<?php

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use DomainException;
use Webmozart\Assert\Assert;

class Token
{
    private string $value;
    private DateTimeImmutable $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expires = $expires;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function validate(string $token, DateTimeImmutable $date): void
    {
        if ($this->value !== $token) {
            throw new DomainException('Incorrect token.');
        }
        if ($date > $this->getExpires()) {
            throw new DomainException('Expired token.');
        }
    }
}
