<?php

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\Token::isExpiredTo
 */
class ExpiresTest extends TestCase
{
    public function testNot()
    {
        $token = new Token(Uuid::uuid4()->toString(), $expires = new DateTimeImmutable());

        self::assertFalse($token->isExpiredTo($expires->modify('-1 secs')));
        self::assertTrue($token->isExpiredTo($expires->modify('+1 secs')));
        self::assertTrue($token->isExpiredTo($expires));
    }
}
