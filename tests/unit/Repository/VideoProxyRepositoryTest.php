<?php

namespace TVListings\Tests\Repository;

use TVListings\Domain\Repository\VideoProxyRepository;
use TVListings\Domain\Entity\VideoProxy;

class VideoProxyRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var VideoProxyRepository
     */
    private $repo;

    protected function setUp()
    {
        $this->entityManager = $this->getMock('TVListings\Domain\Service\EntityManager');
        $this->repo = new VideoProxyRepository($this->entityManager);
    }

    /**
     * @test
     */
    public function it_should_persist_video_proxy_to_db()
    {
        $videoProxy = new VideoProxy("http://videolink.com");

        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->repo->persist($videoProxy);
    }

    /**
     * @test
     */
    public function it_should_find_video_proxy_by_uuid()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('findOneBy');

        $this->repo->find("fake_uuid");
    }
}
