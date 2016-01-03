<?php

namespace TVListings\Domain\Entity;

/**
 * @Entity
 */
class Listing
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Channel")
     * @var Channel
     */
    private $channel;

    /**
     * @Column(type="string")
     * @var string
     */
    private $title;

    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    private $programDate;

    /**
     * @Column(type="string")
     * @var string
     */
    private $programmedTime;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $resourceLink;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @param Channel $channel
     * @param \DateTime $programDate
     */
    public function __construct(
        Channel $channel,
        $title,
        \DateTime $programDate,
        $resourceLink = null
    ) {
        $this->channel = $channel;
        $this->title = $title;
        $this->programDate = $programDate;
        $this->programmedTime = $programDate->format("G:i");
        $this->resourceLink = $resourceLink;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string
     */
    public function changeTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getProgramDate()
    {
       return $this->programDate; 
    }

    /**
     * @param \DateTime $programDate
     */
    public function changeProgramDate(\DateTime $programDate)
    {
        return $this->programDate = $programDate;
    }

    /**
     * @param string $programmedTime
     */
    public function programAt($programmedTime)
    {
        if (null === $programmedTime) {
            throw new \InvalidArgumentException("Program time shouldn't be null");
        }

        preg_match('/\d{1,2}:\d{2}/', $programmedTime, $matches);
        if (0 === count($matches)) {
            throw new \InvalidArgumentException("Invalid program time");
        }

        $changedDateTime = new \DateTime(
            sprintf(
                '%s %s',
                $this->programDate->format('Y-m-d'),
                $programmedTime
            )
        );

        $this->changeProgramDate($changedDateTime);

        //@depricated
        $this->programmedTime = $programmedTime;
    }

    /**
     * @return string
     */
    public function getProgrammedTime()
    {
       return $this->programmedTime; 
    }

    /**
     * @return string
     */
    public function getResourceLink()
    {
        return $this->resourceLink;
    }

    /**
     * @param string $resourceLink
     */
    public function changeResourceLink($resourceLink)
    {
        $this->resourceLink = $resourceLink;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return isset($this->resourceLink) ? true : false;
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
