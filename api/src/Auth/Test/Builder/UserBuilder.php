<?php

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Status;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private Email $email;
    private string $hash;
    private DateTimeImmutable $created;
    private Token $joinConfirmToken;

    private bool $active = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('some-user@mail.com');
        $this->hash = 'hash-hash-hash';
        $this->created = new DateTimeImmutable();
        $this->joinConfirmToken = new Token(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable('+1 day')
        );
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function withJoinConfirmToken(Token $token): self
    {
        $clone = clone $this;
        $clone->joinConfirmToken = $token;
        return $clone;
    }

    public function build(): User
    {
        $user = new User(
            $this->id,
            $this->email,
            $this->created,
            Status::wait()
        );
        $user->setJoinConfirmToken($this->joinConfirmToken);
        $user->setHash($this->hash);
        if ($this->active) {
            $user->confirmJoin(
                $user->getJoinConfirmToken()->getValue(),
                $user->getJoinConfirmToken()->getExpires()->modify('-1 day')
            );
        }
        return $user;
    }
}
