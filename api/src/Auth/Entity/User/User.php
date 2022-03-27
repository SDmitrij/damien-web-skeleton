<?php

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use DomainException;

class User
{
    private Id $id;
    private Email $email;
    private DateTimeImmutable $date;
    private string $hash;
    private Status $status;

    private ?Token $joinConfirmToken;

    public function __construct(Id $id, Email $email, DateTimeImmutable $date, string $hash, Token $token)
    {
        $this->id = $id;
        $this->email = $email;
        $this->date = $date;
        $this->hash = $hash;
        $this->joinConfirmToken = $token;
        $this->status = Status::wait();
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

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
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
}
