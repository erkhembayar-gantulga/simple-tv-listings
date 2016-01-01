<?php

namespace TVListings\Domain\Repository;

use TVListings\Domain\Entity\VideoProxy;
use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Service\EntityManager;

class VideoProxyRepository
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
     * @param VideoProxy $videoProxy
     */
    public function persist(VideoProxy $videoProxy)
    {
        $this->entityManager->persist($videoProxy);
    }

    /**
     * @param string $uuid
     */
    public function find($uuid)
    {
        return $this->entityManager->findOneBy(VideoProxy::class, 'uuid', $uuid);
    }
}
