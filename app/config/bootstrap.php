<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Parser;

require_once __DIR__ . "/../../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../../src/Domain/Entity"), $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/doctrine/mappings", __DIR__."/../../src/Domain/Entity"), $isDevMode);

$yaml = new Parser();
try {
    $configuration = $yaml->parse(file_get_contents(__DIR__ . '/config.yml'));
} catch (ParseException $e) {
    printf("Unable to parse the YAML string: %s", $e->getMessage());
}

$doctrineConfig = $configuration['settings']['doctrine'];

// database configuration parameters
$conn = array(
    'driver' => $doctrineConfig['driver'],
    'host' => $doctrineConfig['host'],
    'dbname' => $doctrineConfig['dbname'],
    'user' => $doctrineConfig['user'],
    'password' => $doctrineConfig['password'],
);

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../../src/Domain/Entity"), true);

$config->setCustomDatetimeFunctions(array(
    'DATE'  => 'DoctrineExtensions\Query\Mysql\Date',
));

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
