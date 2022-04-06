<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use ArrayObject;
use DateTimeImmutable;
use DomainException;

class User
{
    private Id $id;
    private Email $email;
    private DateTimeImmutable $date;
    private Status $status;
    private ArrayObject $networks;

    private ?string $hash = null;
    private ?Token $joinConfirmToken = null;
    private ?Token $passwordResetToken = null;

    public function __construct(Id $id, Email $email, DateTimeImmutable $date, Status $status)
    {
        $this->id = $id;
        $this->email = $email;
        $this->date = $date;
        $this->status = $status;
        $this->networks = new ArrayObject();
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $email, $date, Status::wait());
        $user->hash = $passwordHash;
        $user->joinConfirmToken = $token;

        return $user;
    }

    public static function joinByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ): self {
        $user = new self($id, $email, $date, Status::wait());
        $user->networks->append($identity);
        return $user;
    }

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var NetworkIdentity $existing */
        foreach ($this->networks as $existing) {
            if ($existing->isEqualsTo($identity)) {
                throw new DomainException('Network is already attached.');
            }
        }
        $this->networks->append($identity);
    }

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active');
        }
        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (null === $this->joinConfirmToken) {
            throw new DomainException('Confirmation is not required.');
        }
        $this->joinConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;
        return $this;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function setJoinConfirmToken(Token $token): self
    {
        $this->joinConfirmToken = $token;
        return $this;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function getNetworks(): array
    {
        return $this->networks->getArrayCopy();
    }
}
