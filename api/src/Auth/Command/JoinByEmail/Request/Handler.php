<?php

namespace App\Auth\Command\JoinByEmail\Request;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Service\JoinConfirmationSender;
use App\Auth\Service\PasswordHasher;
use App\Auth\Service\Tokenizer;
use App\Auth\Service\UserRepository;
use App\Flusher;
use DateTimeImmutable;
use DomainException;

class Handler
{
    private JoinConfirmationSender $sender;
    private UserRepository $users;
    private PasswordHasher $hasher;
    private Tokenizer $tokenizer;
    private Flusher $flusher;

    public function __construct(
        JoinConfirmationSender $sender,
        UserRepository $users,
        PasswordHasher $hasher,
        Tokenizer $tokenizer,
        Flusher $flusher
    ) {
        $this->sender = $sender;
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        if ($this->users->hasByEmail($email)) {
            throw new DomainException('User already exists.');
        }
        $user = new User(
            Id::generate(),
            $email,
            $created = new DateTimeImmutable(),
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate($created)
        );
        $this->users->add($user);
        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}
