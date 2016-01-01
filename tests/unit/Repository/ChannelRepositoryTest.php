<?php

namespace TVListings\Tests\Repository;

use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Entity\Listing;
use TVListings\Domain\Repository\ChannelRepository;

class ChannelRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $entityManager;

    protected function setUp()
    {
        $this->entityManager = $this->getMock('TVListings\Domain\Service\EntityManager');
    }

    /**
     * @test
     */
    public function it_should_persist_channel_to_db()
    {
       $repo = new ChannelRepository($this->entityManager);

       $channel = new Channel("MNB", "mnb.png");
       $this->entityManager
           ->expects($this->once())
           ->method('persist');

       $repo->persist($channel);
    }

    /**
     * @test
     */
    public function it_should_delete_channel()
    {
       $repo = new ChannelRepository($this->entityManager);

       $channel = new Channel("MNB", "mnb.png");
       $this->entityManager
           ->expects($this->once())
           ->method('remove')
           ->with($this->equalTo($channel));

       $repo->delete($channel);
    }

    /**
     * @test
     */
    public function it_should_fetch_all_channels()
    {
       $repo = new ChannelRepository($this->entityManager);
       $class = 'TVListings\Domain\Entity\Channel';
       $this->entityManager
           ->expects($this->once())
           ->method('findAll')
           ->with($this->equalTo($class))
           ->willReturn(array());

       $this->assertEquals(array(), $repo->findAll($class));
    }

    /**
     * @test
     */
    public function it_should_fetch_a_channel_by_slug()
    {
       $repo = new ChannelRepository($this->entityManager);
       $channel = new Channel("MNB", "mnb.png");
       $this->entityManager
           ->expects($this->once())
           ->method('findOneBy')
           ->with(
               $this->equalTo(Channel::class),
               $this->equalTo('slug'),
               $this->equalTo($channel->getSlug())
           )
           ->willReturn($channel);

       $this->assertEquals($channel, $repo->findOneBySlug($channel->getSlug()));
    }

    /**
     * @test
     */
    public function it_should_retrieve_todays_listings_by_channel()
    {
       $repo = new ChannelRepository($this->entityManager);
       $channel = new Channel("MNB", "mnb.png");

       $this->entityManager
           ->expects($this->once())
           ->method('findBy')
           ->willReturn(array());

       $this->assertEquals(array(), $repo->getTodayListings($channel));
    }

    /**
     * @test
     */
    public function it_should_retrieve_channel_listings_on_specified_date()
    {
       $repo = new ChannelRepository($this->entityManager);
       $channel = new Channel("MNB", "mnb.png");

       $this->entityManager
           ->expects($this->once())
           ->method('findBy')
           ->willReturn(array());

       $this->assertEquals(array(), $repo->getListingsOf($channel, new \DateTimeImmutable('+1 day')));
    }
}
