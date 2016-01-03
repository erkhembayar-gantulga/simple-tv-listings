<?php

namespace TVListings\Migrations;

use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use TVListings\Domain\Entity\Listing;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160103233927 extends AbstractMigration
{
    /**
     * var EntityManager
     */
    private $em;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $listings = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from(Listing::class, 'e')
            ->getQuery()
            ->getResult();

        foreach ($listings as $listing) {
            preg_match('/\d{1,2}:\d{2}/', $listing->getProgrammedTime(), $matches);
            if (count($matches)) {
                $listing->programAt($listing->getProgrammedTime());
                $this->em->persist($listing);
            }
        }

        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if ($this->em) {
            return $this->em;
        }

        $yaml = new Parser();
        try {
            $configuration = $yaml->parse(file_get_contents(__DIR__ . '/../config/config.yml'));
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
        $this->em = EntityManager::create($conn, $config);

        return $this->em;
    }
}
