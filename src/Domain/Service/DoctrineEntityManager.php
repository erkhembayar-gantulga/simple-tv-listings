<?php

namespace TVListings\Domain\Service;

use Doctrine\ORM\EntityManager as DoctrineORMEntityManager;

class DoctrineEntityManager implements EntityManager
{
    /**
     * @var DoctrineORMEntityManager
     */
    private $em;

    public function __construct(DoctrineORMEntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function remove($entity)
    {
        $this->em->detach($entity);
        $this->em->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function find($class, $identity)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy($class, $identity, $value)
    {
        $query = $this->em->createQueryBuilder()
            ->select('e')
            ->from($class, 'e')
            ->where("e.$identity = :value")
            ->setParameter('value', $value)
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * {@inheritDoc}
     */
    public function findAll($class)
    {
        $query = $this->em->createQueryBuilder()
            ->select('e')
            ->from($class, 'e')
            ->getQuery();

        return $query->getResult();
    }
}
