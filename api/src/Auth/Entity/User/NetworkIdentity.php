<?php

namespace App\Auth\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class NetworkIdentity
{
    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $name;
    /**
     * @ORM\Column(type="string", length=16)
     */
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
