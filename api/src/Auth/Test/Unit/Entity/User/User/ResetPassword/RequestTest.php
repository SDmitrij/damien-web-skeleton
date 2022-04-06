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
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getPasswordResetToken());
        self::assertEquals($token, $user->getPasswordResetToken());
    }

    public function testActive(): void
    {
        $user = (new UserBuilder())->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('User is not active');
        $user->requestPasswordReset($token, $now);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestPasswordReset($token, $now);
        $this->expectExceptionMessage('Resetting is already requested.');
        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestPasswordReset($token, $now);

        $newDate = $now->modify('+2 day');
        $newToken = $this->createToken($newDate->modify('+1 day'));
        $user->requestPasswordReset($newToken, $newDate);

        self::assertEquals($newToken, $user->getPasswordResetToken());
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}