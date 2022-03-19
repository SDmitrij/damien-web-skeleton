<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return static function(): ContainerInterface {
    $builder = new ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/../config/dependencies.php');
    return $builder->build();
};
