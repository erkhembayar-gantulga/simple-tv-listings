<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\QuestionHelper;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . "/../app/config/bootstrap.php";

$cli = new Application('Doctrine Command Line Interface', \Doctrine\ORM\Version::VERSION);

$helperSet = new HelperSet(
    array(
        'db' => new ConnectionHelper($entityManager->getConnection()),
        'em' => new EntityManagerHelper($entityManager),
        'dialog' => new QuestionHelper(),
    )
);

$cli->setHelperSet($helperSet);

$outputWriter = new OutputWriter(function ($message) {
    $output = new ConsoleOutput();
    $output->writeln($message);
});

$config = new Configuration($entityManager->getConnection(), $outputWriter);

$config->setMigrationsDirectory(__DIR__ . $configuration['migrations']['directory']);
$config->setName($configuration['migrations']['name']);
$config->setMigrationsNamespace($configuration['migrations']['namespace']);
$config->setMigrationsTableName($configuration['migrations']['table_name']);

$config->registerMigrationsFromDirectory(__DIR__ . $configuration['migrations']['directory']);

$commands = array(
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
);

foreach ($commands as $command) {
    $command->setMigrationConfiguration($config);
    $cli->add($command);
}

ConsoleRunner::addCommands($cli);

$cli->run();
