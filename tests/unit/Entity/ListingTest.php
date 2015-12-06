<?php

namespace TVListings\Tests\Entity;

use TVListings\Domain\Entity\Listing;

class ListingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Channel
     */
    private $channel;

    protected function setUp()
    {
        $this->channel = $this->getMockBuilder('TVListings\Domain\Entity\Channel')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function it_should_be_created_with_channel_and_program_date()
    {
        $listing = new Listing(
            $this->channel,
            "News",
            new \DateTime()
        );

        $this->assertInstanceOf('TVListings\Domain\Entity\Channel', $listing->getChannel());
        $this->assertEquals($this->channel, $listing->getChannel());
        $this->assertEquals("News", $listing->getTitle());
        $this->assertEquals(new \DateTime(), $listing->getProgramDate());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_be_programable_by_time()
    {
        $listing = new Listing(
            $this->channel,
            "News",
            new \DateTime("2015-11-28 18:00")
        );

        $this->assertEquals("18:00", $listing->getProgrammedTime());
        $listing->programAt("20:00");

        $this->assertEquals("20:00", $listing->getProgrammedTime());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_change_resource_link()
    {
        $listing = new Listing(
            $this->channel,
            "News",
            new \DateTime("2015-11-28 18:00"),
            "http://wronglink"
        );

        $this->assertEquals("http://wronglink", $listing->getResourceLink());
        $listing->changeResourceLink("http://test.com/hello");

        $this->assertEquals("http://test.com/hello", $listing->getResourceLink());
    }

    /**
     * @test
     */
    public function it_should_be_active_if_resource_link_is_provided()
    {
        $listing = new Listing(
            $this->channel,
            "News",
            new \DateTime("2015-11-28 18:00")
        );

        $this->assertEquals(null, $listing->getResourceLink());
        $this->assertFalse($listing->isActive());

        $listing->changeResourceLink("http://test.com/hello");
        $this->assertTrue($listing->isActive());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_change_description()
    {
        $listing = new Listing(
            $this->channel,
            "News",
            new \DateTime("2015-11-28 18:00")
        );

        $this->assertEquals(null, $listing->getDescription());
        $listing->setDescription("Listing details are here");

        $this->assertEquals("Listing details are here", $listing->getDescription());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_should_guard_againts_programmed_time_null_value()
    {
        $listing = new Listing(
            $this->channel,
            "News",
            new \DateTime("2015-11-28 18:00")
        );

        $listing->programAt(null);
    }
}
