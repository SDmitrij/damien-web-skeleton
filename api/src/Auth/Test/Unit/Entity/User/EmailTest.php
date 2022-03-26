<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\Email
 */
class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new Email($value = 'damien-side@fun.com');
        self::assertEquals($value, $email->getValue());
    }

    public function testCase(): void
    {
        $email = new Email($value = 'DaMiEn-Side@fuN.Com');
        self::assertEquals('damien-side@fun.com', $email->getValue());
    }

    public function testInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-an-email');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}
