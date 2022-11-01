<?php

use App\Auth\Entity\User\User;
use App\Auth\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    UserRepository::class => static function(ContainerInterface $container) {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        return new UserRepository($em, $em->getRepository(User::class));
    },
];