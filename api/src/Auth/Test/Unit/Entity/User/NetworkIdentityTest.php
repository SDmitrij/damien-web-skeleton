<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\NetworkIdentity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Auth\Entity\User\NetworkIdentity
 */
class NetworkIdentityTest extends TestCase
{
    public function testSuccess(): void
    {
        $network = new NetworkIdentity($name = 'runetki', $id = 'user-1');

        self::assertEquals($name, $network->getName());
        self::assertEquals($id, $network->getIdentity());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NetworkIdentity('', 'user-2');
    }

    public function testCase(): void
    {
        $name = 'runetki';
        $id = 'user-1';
        $network = new NetworkIdentity(mb_strtoupper($name), mb_strtoupper($id));

        self::assertEquals($name, $network->getName());
        self::assertEquals($id, $network->getIdentity());
    }

    public function testIsEqualsTo(): void
    {
        $network = new NetworkIdentity($name = 'runetki', $id = 'user-1');

        self::assertTrue($network->isEqualsTo(new NetworkIdentity($name, $id)));
        self::assertFalse($network->isEqualsTo(new NetworkIdentity($name, 'user-3')));
        self::assertFalse($network->isEqualsTo(new NetworkIdentity('vk', 'user-3')));
    }
}