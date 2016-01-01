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
    private $entityManager;

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
     * @param int $id
     */
    public function find($id)
    {
        return $this->entityManager->find(Listing::class, $id);
    }

    /**
     * @param Listing $listing
     */
    public function delete($listing)
    {
        return $this->entityManager->remove($listing);
    }

    /**
     * @param Channel $channel
     */
    public function findBy(Channel $channel)
    {
        $criteria = array(
            'channel' => array(
                'builder' => function ($alias) {
                    return sprintf("%s.channel", $alias);
                },
                'value' => $channel
            ),
        );

        return $this->entityManager->findBy(
            Listing::class,
            $criteria
        );
    }
}
