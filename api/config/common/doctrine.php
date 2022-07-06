<?php

use App\Auth\Entity\User\EmailType;
use App\Auth\Entity\User\IdType;
use App\Auth\Entity\User\RoleType;
use App\Auth\Entity\User\StatusType;
use DI\Container;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    EntityManagerInterface::class => function (Container $container): EntityManagerInterface {
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAnnotationMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ?
                new FilesystemAdapter('', 0, $settings['cache_dir']) :
                new ArrayAdapter(),
        );
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        return EntityManager::create($settings['connection'], $config);
    },
    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'utf-8'
            ],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Entity'
            ],
            'types' => [
                IdType::NAME => IdType::class,
                EmailType::NAME => EmailType::class,
                RoleType::NAME => RoleType::class,
                StatusType::NAME => StatusType::class
            ]
        ]
    ]
];
