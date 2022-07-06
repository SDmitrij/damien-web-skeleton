<?php

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
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
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-email@mail.ru'));

        self::assertNotNull($user->getNewEmailToken());

        $user->confirmEmailChanging($token->getValue(), new DateTimeImmutable());

        self::assertEquals($user->getEmail(), $new);
        self::assertNull($user->getNewEmail());
        self::assertNull($user->getNewEmailToken());
    }

    public function testIsNotRequested(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Changing is not requested.');

        $user->confirmEmailChanging($token->getValue(), new DateTimeImmutable());
    }

    public function testIncorrect(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-email@mail.ru'));

        $this->expectExceptionMessage('Incorrect token.');

        $user->confirmEmailChanging(Uuid::uuid4()->toString(), new DateTimeImmutable());
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-email@mail.ru'));

        $this->expectExceptionMessage('Expired token.');

        $user->confirmEmailChanging($token->getValue(), $now->modify('+2 days'));
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}