<?php

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('some-mail@mail.ru'))
            ->active()
            ->build();
        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-email@mail.ru'));

        self::assertEquals($token->getValue(), $user->getNewEmailToken()->getValue());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testSame(): void
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('some-mail@mail.ru'))
            ->active()
            ->build();
        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Email is already in use.');

        $user->requestEmailChanging($token, $now, $old);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('some-mail@mail.ru'))
            ->active()
            ->build();
        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-mail@mail.com'));

        $this->expectExceptionMessage('Changing is already requested.');

        $user->requestEmailChanging($token, $now, new Email('some-new-mail@mail.com'));
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder())
            ->withEmail(new Email('some-mail@mail.ru'))
            ->active()
            ->build();
        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestEmailChanging($token, $now, new Email('new-mail@mail.com'));

        $newDate = $now->modify('+2 hours');
        $newToken = $this->createToken($newDate->modify('+1 hour'));

        $user->requestEmailChanging($newToken, $newDate, $newEmail = new Email('some-new-mail@mail.com'));

        self::assertEquals($newToken, $user->getNewEmailToken());
        self::assertEquals($newEmail, $user->getNewEmail());
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(Uuid::uuid4()->toString(), $date);
    }
}
