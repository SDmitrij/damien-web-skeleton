<?php

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\User;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function hasByNetwork(NetworkIdentity $identity): bool;
    public function findByConfirmToken(string $token): ?User;
    public function add(User $user): void;
}
