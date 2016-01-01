<?php

namespace TVListings\Tests\Service;

use Ramsey\Uuid\Uuid;
use TVListings\Domain\Entity\VideoProxy;

class VideoProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_reference_given_video_source()
    {
       $videoProxy = new VideoProxy("http://videolink");

       $this->assertEquals("http://videolink", $videoProxy->getSource());
       $this->assertTrue(Uuid::isValid($videoProxy->getUuid()));
    }
}
