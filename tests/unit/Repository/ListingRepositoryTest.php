<?php

namespace TVListings\Tests\Repository;

use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Entity\Listing;
use TVListings\Domain\Repository\ListingRepository;

class ListingRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $entityManager;
    private $repo;

    protected function setUp()
    {
        $this->entityManager = $this->getMock('TVListings\Domain\Service\EntityManager');
       $this->repo = new ListingRepository($this->entityManager);
    }

    /**
     * @test
     */
    public function it_should_persist_channel_to_db()
    {
       $channel = new Channel("MNB", 'mnb.png');
       $listing = new Listing($channel, "News", new \DateTime());

       $this->entityManager
           ->expects($this->once())
           ->method('persist');

       $this->repo->persist($listing);
    }

    /**
     * @test
     */
    public function it_should_retrieve_all_listings_by_channel()
    {
       $channel = new Channel("MNB", 'mnb.png');

       $this->entityManager
           ->expects($this->once())
           ->method('findBy')
           ->willReturn(array());

       $this->assertEquals(array(), $this->repo->findBy($channel));
    }
}
