<?php

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Token
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $expires;

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

    /** @noinspection PhpMissingReturnTypeInspection */
    public function getExpires()
    {
        return $this->expires;
    }

    public function validate(string $token, DateTimeImmutable $date): void
    {
        if ($this->value !== $token) {
            throw new DomainException('Incorrect token.');
        }
        if ($this->isExpiredTo($date)) {
            throw new DomainException('Expired token.');
        }
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $date >= $this->expires;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }
}
