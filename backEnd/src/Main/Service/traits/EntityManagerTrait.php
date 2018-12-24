<?php

namespace Main\Service\traits;

use Doctrine\ORM\EntityManager;

trait EntityManagerTrait
{
    /** @var EntityManager */
    protected $entityManager;

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}
