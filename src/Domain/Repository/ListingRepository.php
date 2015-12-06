<?php

namespace TVListings\Domain\Repository;

use TVListings\Domain\Entity\Listing;
use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Service\EntityManager;

class ListingRepository
{
    /**
     * @var EntityManager
     */
    private $listing;

    public function __construct(EntityManager $entityManager)
    {
       $this->entityManager = $entityManager; 
    }

    /**
     * @param Listing $listing
     */
    public function persist(Listing $listing)
    {
        $this->entityManager->persist($listing);
    }

    /**
     * @param Channel $channel
     */
    public function findBy(Channel $channel)
    {
        $criteria = array();

        return $this->entityManager->findBy(
            Listing::class,
            $criteria
        );
    }
}
