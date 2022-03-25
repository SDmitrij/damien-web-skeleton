<?php

namespace App\Auth\Entity\User;

use DateTimeImmutable;

class User
{
    private Id $id;
    private Email $email;
    private DateTimeImmutable $date;
    private string $hash;
    private ?Token $token;

    public function __construct(Id $id, Email $email, DateTimeImmutable $date, string $hash, ?Token $token)
    {
        $this->id = $id;
        $this->email = $email;
        $this->date = $date;
        $this->hash = $hash;
        $this->token = $token;
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

    public function getToken(): ?Token
    {
        return $this->token;
    }
}