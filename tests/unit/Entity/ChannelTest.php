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
        $channel = new Channel("MNB", $this->getAbsoluteDir("/logos/mnb.png"));

        $this->assertEquals("MNB", $channel->getName());
        $this->assertEquals($this->getAbsoluteDir("/logos/mnb.png"), $channel->getLogoPath());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_should_validate_path_logo_file()
    {
        new Channel("MNB", "/logos/non-existing.png");
    }

    /**
     * @test
     */
    public function it_should_make_lowercase_for_channel_slug()
    {
        $channel = new Channel("MnB", $this->getAbsoluteDir("/logos/mnb.png"));
        $this->assertEquals("mnb", $channel->getSlug());
    }

    /**
     * @test
     */
    public function it_should_be_able_to_change_description()
    {
        $channel = new Channel("MNB", $this->getAbsoluteDir("/logos/mnb.png"));
        $channel->setDescription("Mongolian National Broadcasting");

        $this->assertEquals("Mongolian National Broadcasting", $channel->getDescription());
    }

    /**
     * @return string
     */
    private function getAbsoluteDir($path)
    {
        return __DIR__ . '/../../../public' . $path;
    }
}
