<?php

declare(strict_types=1);

namespace App\Auth\Command\ResetPassword\Reset;

use App\Auth\Service\PasswordHasher;
use App\Auth\Service\UserRepository;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    private UserRepository $users;
    private PasswordHasher $hasher;
    private Flusher $flusher;

    public function __construct(UserRepository $users, PasswordHasher $hasher, Flusher $flusher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByPasswordResetToken($command->token)) {
            throw new DomainException('Token is not found.');
        }
        $user->resetPassword(
            $command->token,
            new DateTimeImmutable(),
            $command->password,
            $this->hasher
        );

        $this->flusher->flush();
    }
}