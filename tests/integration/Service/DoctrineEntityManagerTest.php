<?php

namespace TVListings\Tests\Integration\Service;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Service\DoctrineEntityManager;

class DoctrineEntityManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    protected function setUp()
    {
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../../../src/Domain/Entity"), true);

        // database configuration parameters
        $conn = array(
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../../app/config/db.sqlite',
        );

        // obtaining the entity manager
        $this->em = EntityManager::create($conn, $config);
    }

    /**
     * @test
     * @expectedException Doctrine\DBAL\Exception\UniqueConstraintViolationException
     */
    public function it_should_throw_duplicated_exception_when_channel_is_already_exist()
    {
        $em = new DoctrineEntityManager($this->em);

        $channel = new Channel("MNB", __DIR__ . "/../../../public/logos/mnb.png");
        $em->persist($channel);

        $channel = new Channel("MNB", __DIR__ . "/../../../public/logos/mnb.png");
        $em->persist($channel);
    }

    /**
     * @test
     */
    public function it_should_find_a_channel_by_slug()
    {
        $em = new DoctrineEntityManager($this->em);

        $this->assertEquals("MNB", $em->findOneBy("TVListings\Domain\Entity\Channel", "slug", "mnb")->getName());
    }
}
