<?php

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\User
 */
class ChangePasswordTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user->changePassword('old-password', 'new-password', $hasher);

        self::assertEquals($hash, $user->getHash());
    }

    public function testIncorrectPassword(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(false);

        $this->expectExceptionMessage('Incorrect current password.');

        $user->changePassword('wrong-old-password', 'new-password', $hasher);
    }

    public function testByNetwork(): void
    {
        $user = (new UserBuilder())
            ->viaNetwork()
            ->build();

        $hasher = $this->createHasher(true);

        $this->expectExceptionMessage('User does not have an old password.');

        $user->changePassword('any-old-password', 'new-password', $hasher);
    }

    private function createHasher(bool $validate, string $hash = ''): PasswordHasher
    {
        $hasher = $this->createStub(PasswordHasher::class);

        $hasher->method('validate')->willReturn($validate);
        $hasher->method('hash')->willReturn($hash);

        return $hasher;
    }
}
