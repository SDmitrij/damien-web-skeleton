<?php

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class NetworkIdentity
{
    private string $name;
    private string $identity;

    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($identity);

        $this->name = mb_strtolower($name);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualsTo(self $networkIdentity): bool
    {
        return
            $this->getName() === $networkIdentity->getName() &&
            $this->getIdentity() === $networkIdentity->getIdentity();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIdentity()
    {
        return $this->identity;
    }
}
