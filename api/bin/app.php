<?php

declare(strict_types=1);

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\AbstractEntityManagerCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

/** @var ContainerInterface $container */
$container = (require __DIR__ . '/../config/container.php')();

$cli = new Application('Console');
$commands = $container->get('config')['console']['commands'];

/** @var EntityManagerInterface $em */
$em = $container->get(EntityManagerInterface::class);

$conf = new Configuration();
$conf->addMigrationsDirectory('App\Data\Migration', __DIR__ . '/../src/Data/Migration');
$conf->setAllOrNothing(true);

$storageConf = new TableMetadataStorageConfiguration();
$storageConf->setTableName('migrations');

$conf->setMetadataStorageConfiguration($storageConf);

$dependencyFactory = DependencyFactory::fromEntityManager(
    new ExistingConfiguration($conf),
    new ExistingEntityManager($em)
);
$provider = new Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($em);

foreach ($commands as $command) {
    if (is_a($command, AbstractEntityManagerCommand::class, true)) {
        $cli->add(new $command($provider));
    } elseif (is_a($command, DoctrineCommand::class, true)) {
        $cli->add(new $command($dependencyFactory));
    } else {
        $cli->add($container->get($command));
    }
}
$cli->run();
