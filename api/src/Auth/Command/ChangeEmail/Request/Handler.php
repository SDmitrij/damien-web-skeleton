<?php

namespace App\Auth\Command\ChangeEmail\Request;

use App\Auth\Entity\User\Id;
use App\Auth\Service\Tokenizer;
use App\Auth\Service\UserRepository;
use App\Flusher;

class Handler
{
    private UserRepository $users;
    private Tokenizer $tokenizer;
    private NewEmailConfirmTokenSender $sender;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        Tokenizer $tokenizer,
        NewEmailConfirmTokenSender $sender,
        Flusher $flusher
    ) {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

    }
}