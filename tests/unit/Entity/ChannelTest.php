<?php

namespace TVListings\Tests\Entity;

use TVListings\Domain\Entity\Channel;

class ChannelTest extends \PHPUnit_Framework_TestCase
{
    private $assetsRootDirectory;

    /**
     * @test
     */
    public function it_should_be_created_with_name_and_logo()
    {
        $channel = new Channel("MNB", "mnb.png");

        $this->assertEquals("MNB", $channel->getName());
        $this->assertEquals("mnb.png", $channel->getLogoPath());
    }

    /**
     * @test
     */
    public function it_should_make_lowercase_for_channel_slug()
    {
        $channel = new Channel("MnB", "mnb.png");
        $this->assertEquals("mnb", $channel->getSlug());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_change_description()
    {
        $channel = new Channel("MNB", "mnb.png");
        $channel->setDescription("Mongolian National Broadcasting");

        $this->assertEquals("Mongolian National Broadcasting", $channel->getDescription());
    }
}
