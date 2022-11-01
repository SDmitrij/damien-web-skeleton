<?php

use App\Frontend\FrontendUrlTwigExtension;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

return [
    Environment::class => function(ContainerInterface $container) {
        $config = $container->get('config')['twig'];
        $loader = new FilesystemLoader;

        foreach ($config['template_dirs'] as $alias => $dir)
            $loader->addPath($dir, $alias);

        $env = new Environment($loader, [
            'cache' => $config['debug'] ? false : $config['cache_dir'],
            'debug' => $config['debug'],
            'strict_variables' => $config['debug'],
            'auto_reload' => $config['debug']
        ]);
        if ($config['debug'])
            $env->addExtension(new DebugExtension);
        foreach ($config['extensions'] as $class)
            $env->addExtension($container->get($class));
    },

    'config' => [
        'twig' => [
            'debug' => (bool)getenv('APP_DEBUG'),
            'template_dirs' => [
                FilesystemLoader::MAIN_NAMESPACE => __DIR__ . '/../../templates'
            ],
            'cache_dir' => __DIR__ . '/../../var/cache/twig',
            'extensions' => [FrontendUrlTwigExtension::class]
        ]
    ]
];