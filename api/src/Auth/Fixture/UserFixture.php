<?php

/** @noinspection SpellCheckingInspection */
namespace App\Auth\Fixture;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class UserFixture implements FixtureInterface
{

    public const PASSWORD_HASH = '$argon2i$v=19$m=65536,t=4,p=1$LldNLk4xVmJiMmRJTkFIUw$Io39LXhjn0AXMciJtJVAe25FUnWPdQc01Viw+eGOS4I';

    public function load(ObjectManager $manager)
    {
        $user = User::requestJoinByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            $date = new DateTimeImmutable('-30 days'),
            new Email('user@app.test'),
            self::PASSWORD_HASH,
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );
        $user->confirmJoin($value, $date);

        $manager->persist($user);
        $manager->flush();
    }
}