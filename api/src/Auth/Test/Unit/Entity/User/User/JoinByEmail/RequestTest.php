<?php

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = User::requestJoinByEmail(
            $id = Id::generate(),
            $created = new DateTimeImmutable(),
            $email = new Email('some-email@com.ru'),
            $hash = 'hash-hash',
            $token = new Token(
                Uuid::uuid4()->toString(),
                new DateTimeImmutable('+1 day')
            )
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($created, $user->getDate());
        self::assertEquals($hash, $user->getHash());
        self::assertEquals($token, $user->getJoinConfirmToken());

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }
}
