<?php

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Entity\User\User;
use DomainException;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function hasByNetwork(NetworkIdentity $identity): bool;
    public function findByConfirmToken(string $token): ?User;
    public function findByPasswordResetToken(string $token): ?User;
    public function add(User $user): void;
    /**
     * @throws DomainException
     */
    public function get(Id $id): User;
    /**
     * @throws DomainException
     */
    public function getByEmail(Email $email): User;
}
