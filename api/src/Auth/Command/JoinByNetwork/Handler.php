<?php

namespace App\Auth\Command\JoinByNetwork;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\User;
use App\Auth\Service\UserRepository;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(UserRepository $users, Flusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $identity = new NetworkIdentity($command->network, $command->identity);
        $email = new Email($command->email);
        if ($this->users->hasByNetwork($identity)) {
            throw new DomainException('User with this network already exists.');
        }
        if ($this->users->hasByEmail($email)) {
            throw new DomainException('User with this email already exists.');
        }
        $user = User::joinByNetwork(
            Id::generate(),
            new DateTimeImmutable(),
            $email,
            $identity
        );
        $this->users->add($user);
        $this->flusher->flush();
    }
}
