<?php

namespace TVListings\Domain\Entity;

/**
 * @Entity
 */
class Channel
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    private $name;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    private $slug;

    /**
     * @Column(type="string")
     * @var string
     */
    private $logoPath;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @param string $name      Name of the channel
     * @param string $logoPath  Absolute path of the file
     * @throws \InvalidArgumentException
     */
    public function __construct($name, $logoPath)
    {
        $this->name = $name;

        if (!file_exists($logoPath)) {
            throw new \InvalidArgumentException("A logo doesn't exist.");
        }

        $this->logoPath = $logoPath;
        $this->slug = strtolower($this->name);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
       return $this->description; 
    }
}
