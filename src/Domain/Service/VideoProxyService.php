<?php

namespace TVListings\Domain\Service;

use Ramsey\Uuid\Uuid;
use TVListings\Domain\Entity\VideoProxy;

class VideoProxyService
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
     * @param string $source
     * @return string
     */
    public function createFromSource($source)
    {
        $videoProxy = new VideoProxy($source);
        $this->entityManager->persist($videoProxy);

        return (string) $videoProxy->getUuid();
    }
}
