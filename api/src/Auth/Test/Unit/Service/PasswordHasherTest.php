<?php

namespace App\Auth\Test\Unit\Service;

use App\Auth\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Service\PasswordHasher
 */
class PasswordHasherTest extends TestCase
{
    public function testSuccess(): void
    {
        $hasher = new PasswordHasher(16);
        $hash = $hasher->hash('some-password');

        self::assertNotEmpty($hash);
        self::assertNotEquals('some-password', $hash);
        self::assertTrue($hasher->validate('some-password', $hash));
    }
}
