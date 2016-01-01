<?php

namespace TVListings\Tests\Repository;

use Ramsey\Uuid\Uuid;
use TVListings\Domain\Service\VideoProxyService;

class VideoProxyServiceTest extends \PHPUnit_Framework_TestCase
{
    private $entityManager;

    protected function setUp()
    {
        $this->entityManager = $this->getMock('TVListings\Domain\Service\EntityManager');
    }

    /**
     * @test
     */
    public function it_should_create_video_proxy_from_the_video_source()
    {
       $videoProxyService = new VideoProxyService($this->entityManager);

       $this->entityManager
           ->expects($this->once())
           ->method('persist');
       $proxyUuid = $videoProxyService->createFromSource("http://video-proxy.com");

       $this->assertEquals($proxyUuid, (string) Uuid::fromString($proxyUuid));
    }
}
