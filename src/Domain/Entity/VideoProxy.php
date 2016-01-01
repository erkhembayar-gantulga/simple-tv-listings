<?php

namespace TVListings\Domain\Entity;

use Ramsey\Uuid\Uuid;

/**
 * @Entity
 */
class VideoProxy
{
    /**
     * @Id
     * @Column(type="string", unique=true)
     * @var Uuid
     */
    private $uuid;

    /**
     * @Column(type="string")
     * @var string
     */
    private $source;

    /**
     * @return string
     */
    public function __construct($source)
    {
        if (null === $source || "" === $source) {
            throw new \InvalidArgumentException("Video source shouldn't be empty.");
        }

        $this->source = $source;
        $this->uuid = Uuid::uuid4();
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
