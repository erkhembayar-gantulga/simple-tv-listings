<?php

namespace TVListings\Domain\Service;

interface EntityManager
{
    /**
     * @param $entity
     */
    public function persist($entity);

    /**
     * @param $entity
     */
    public function remove($entity);

    /**
     * @param string $class
     * @param string|int $identity
     */
    public function find($class, $identity);

    /**
     * @param string $class
     * @param string $identity
     * @param string $value
     */
    public function findOneBy($class, $identity, $value);

    /**
     * @param string $class
     * @param array $criteria
     */
    public function findBy($class, $criteria);

    /**
     * @param string $class
     */
    public function findAll($class);
}
