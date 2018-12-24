<?php

namespace Main\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

abstract class BaseRepository extends EntityRepository
{
    /**
     * @return int
     * @throws DBALException
     */
    public function truncate(): int
    {
        return $this->_em->getConnection()->exec('TRUNCATE '.$this->getClassMetadata()->getTableName());
    }

    /**
     * @return int
     * @throws DBALException
     */
    public function deleteAllByOne(): int
    {
        return $this->_em->getConnection()->exec('DELETE FROM '.$this->getClassMetadata()->getTableName());
    }
}
