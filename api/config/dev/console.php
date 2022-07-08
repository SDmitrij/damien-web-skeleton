<?php

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                DropCommand::class,
                UpdateCommand::class,

                DiffCommand::class,
                GenerateCommand::class
            ]
        ]
    ]
];