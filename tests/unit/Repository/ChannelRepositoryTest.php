<?php

namespace TVListings\Tests\Entity;

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

       $channel = new Channel("MNB", __DIR__ . "/../../../public/logos/mnb.png");
       $this->entityManager
           ->expects($this->once())
           ->method('persist');

       $repo->persist($channel);
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
           ->with($this->equalTo($class));

       $repo->findAll($class);
    }
}
