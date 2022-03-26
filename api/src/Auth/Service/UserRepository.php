<?php

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\User;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function add(User $user): void;
}
