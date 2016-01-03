<?php

namespace TVListings\Domain\Repository;

use TVListings\Domain\Entity\Channel;
use TVListings\Domain\Entity\Listing;
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
     * @param Channel $channel
     */
    public function delete(Channel $channel)
    {
        $this->entityManager->remove($channel);
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->entityManager->findAll("TVListings\Domain\Entity\Channel");
    }

    /**
     * @param string slug
     */
    public function findOneBySlug($slug)
    {
        return $this->entityManager->findOneBy(Channel::class, 'slug', $slug);
    }

    /**
     * @param Channel $channel
     * @return array
     */
    public function getTodayListings(Channel $channel)
    {
        $criteria = array(
            'channel' => array(
                'builder' => function ($alias) {
                    return sprintf("%s.channel", $alias);
                },
                'value' => $channel
            ),
            'programDate' => array(
               'builder' => function ($alias) {
                    return sprintf("DATE(%s.programDate)", $alias);
               },
               'value' => (new \DateTime())->format('Y-m-d')
            ),
            'orderBy' => array(
               'builder' => function ($alias) {
                    return sprintf("%s.programDate", $alias);
               },
               'value' => 'ASC',
            ),
        );

        return $this->entityManager->findBy(
            Listing::class,
            $criteria
        );
    }

    /**
     * @param Channel $channel
     * @param \DateTimeImmutable $specifiedDate
     * @return array
     */
    public function getListingsOf(Channel $channel, \DateTimeImmutable $specifiedDate)
    {
        $criteria = array(
            'channel' => array(
                'builder' => function ($alias) {
                    return sprintf("%s.channel", $alias);
                },
                'value' => $channel
            ),
            'programDate' => array(
               'builder' => function ($alias) {
                    return sprintf("DATE(%s.programDate)", $alias);
               },
               'value' => $specifiedDate->format('Y-m-d')
            ),
            'orderBy' => array(
               'builder' => function ($alias) {
                    return sprintf("%s.programDate", $alias);
               },
               'value' => 'ASC',
            ),
        );

        return $this->entityManager->findBy(
            Listing::class,
            $criteria
        );
    }
}
