<?php

namespace App\Auth\Command\JoinByEmail\Confirm;

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
        if (!$user = $this->users->findByConfirmToken($command->token)) {
            throw new DomainException('Incorrect token.');
        }
        $user->confirmJoin($command->token, new DateTimeImmutable());
        $this->flusher->flush();
    }
}
