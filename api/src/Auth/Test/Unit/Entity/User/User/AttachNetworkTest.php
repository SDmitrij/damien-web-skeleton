<?php

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

class AttachNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $identity = new NetworkIdentity('vk', 'vk-user-one');
        $user = (new UserBuilder())->build();
        $user->attachNetwork($identity);

        self::assertCount(1, $user->getNetworks());
        self::assertTrue($identity->isEqualsTo($user->getNetworks()[0]));
    }
}
