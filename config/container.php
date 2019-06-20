<?php
declare(strict_types=1);

// Using a self-called anonymous function to create its own scope and keep the variables away from the global scope.
return (function () {
    $builder = new \DI\ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/container-definitions.php');

    $container = $builder->build();

    return $container;
})();

