<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class TokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token($value = Uuid::uuid4()->toString(), $expires = new DateTimeImmutable('+1 day'));
        self::assertEquals($value, $token->getValue());
        self::assertEquals($expires, $token->getExpires());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $token = new Token(mb_strtoupper($value), new DateTimeImmutable('+1 day'));
        self::assertEquals($value, $token->getValue());
    }
}