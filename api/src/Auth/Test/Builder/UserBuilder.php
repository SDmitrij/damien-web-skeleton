<?php

namespace App\Auth\Test\Builder;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\Status;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class UserBuilder
{
    private Id $id;
    private Email $email;
    private DateTimeImmutable $created;
    private Token $joinConfirmToken;
    private string $hash;

    private ?NetworkIdentity $identity = null;
    private bool $active = false;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('some-user@mail.com');
        $this->created = new DateTimeImmutable();
        $this->joinConfirmToken = new Token(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable('+1 day')
        );
        $this->hash = 'hash';
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

    public function viaNetwork(NetworkIdentity $identity = null): self
    {
        $clone = clone $this;
        $clone->identity = $identity ?? new NetworkIdentity('vk', '0001');
        return $clone;
    }

    public function withEmail(Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function build(): User
    {
        if (null !== $this->identity) {
            return User::joinByNetwork(
                $this->id,
                $this->created,
                $this->email,
                $this->identity
            );
        }
        $user = User::requestJoinByEmail(
            $this->id,
            $this->created,
            $this->email,
            $this->hash,
            $this->joinConfirmToken
        );

        if ($this->active) {
            $user->confirmJoin(
                $user->getJoinConfirmToken()->getValue(),
                $user->getJoinConfirmToken()->getExpires()->modify('-1 day')
            );
        }
        return $user;
    }
}
