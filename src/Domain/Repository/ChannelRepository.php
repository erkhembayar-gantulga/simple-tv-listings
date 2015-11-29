<?php

namespace TVListings\Domain\Repository;

use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Service\EntityManager;

class ChannelRepository
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
     * @param Channel $channel
     */
    public function persist(Channel $channel)
    {
        $this->entityManager->persist($channel);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->entityManager->findAll("TVListings\Domain\Entity\Channel");
    }
}
