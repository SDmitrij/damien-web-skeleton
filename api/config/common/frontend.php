<?php

use App\Frontend\FrontendUrlGenerator;
use Psr\Container\ContainerInterface;

return [
    FrontendUrlGenerator::class => function (ContainerInterface $container) {
        return new FrontendUrlGenerator(
            $container->get('config')['frontend']['url']
        );
    },

    'config' => [
        'frontend' => [
            'url' => getenv('FRONTEND_URL')
        ]
    ]
];