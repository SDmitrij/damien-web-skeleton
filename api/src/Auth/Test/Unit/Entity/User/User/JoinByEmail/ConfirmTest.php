<?php

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());
        self::assertNull($user->getJoinConfirmToken());
    }

    public function testIncorrect(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Incorrect token.');
        $user->confirmJoin(Uuid::uuid4()->toString(), $token->getExpires()->modify('-1 day'));
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Expired token.');
        $user->confirmJoin($token->getValue(), $token->getExpires()->modify('+1 day'));
    }

    public function testAlreadyActive(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();
        $this->expectExceptionMessage('Confirmation is not required.');
        $user->confirmJoin($token->getValue(), $token->getExpires()->modify('-1 day'));
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new DateTimeImmutable('+1 day')
        );
    }
}
