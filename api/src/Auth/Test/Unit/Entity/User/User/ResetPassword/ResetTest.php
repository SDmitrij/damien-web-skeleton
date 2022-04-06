<?php

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers \App\Auth\Entity\User\User
 */
class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());

        $user->resetPassword($token->getValue(), $now, $hash = 'hash-hash');

        self::assertNull($user->getPasswordResetToken());
        self::assertEquals($hash, $user->getHash());
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}