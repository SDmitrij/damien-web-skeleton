<?php

namespace App\Auth\Command\JoinByEmail\Request;

class Command
{
    public string $email = '';
    public string $password = '';
}