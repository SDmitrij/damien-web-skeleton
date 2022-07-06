<?php

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        Assert::email($value);

        $this->value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqualTo(Email $email): bool
    {
        return $this->getValue() === $email->getValue();
    }
}
