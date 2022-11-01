<?php

use App\Auth\Service\JoinConfirmationSender;
use App\Console\FixturesLoadCommand;
use App\Console\MailerCheckCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;
use Psr\Container\ContainerInterface;

return [
    FixturesLoadCommand::class => static function(ContainerInterface $container) {
        $config = $container->get('config')['console'];
        $em = $container->get(EntityManagerInterface::class);

        return new FixturesLoadCommand($em, $config['fixture_paths']);
    },
    MailerCheckCommand::class => static function(ContainerInterface $container) {
        return new MailerCheckCommand($container->get(JoinConfirmationSender::class));
    },
    'config' => [
        'console' => [
            'commands' => [
                DropCommand::class,
                UpdateCommand::class,

                DiffCommand::class,
                GenerateCommand::class,

                FixturesLoadCommand::class,

                MailerCheckCommand::class
            ],
            'fixture_paths' => [
                __DIR__ . '/../../src/Auth/Fixture'
            ]
        ]
    ]
];