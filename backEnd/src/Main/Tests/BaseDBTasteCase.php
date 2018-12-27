<?php

namespace Main\Tests;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\Provider\OrmSchemaProvider;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

abstract class BaseDBTasteCase extends TestCase
{
    /**
     * @param array $classNames
     * @param EntityManager $em
     * @throws DBALException
     * @throws SchemaException
     */
    public function createTables(array $classNames, EntityManager $em)
    {
        $platform = $em->getConnection()->getDatabasePlatform();
        $toSchema = (new OrmSchemaProvider($em))->createSchema();
        $createSqls = [];
        foreach ($classNames as $className) {
            $metaData = $em->getClassMetadata($className);
            $tableName = $metaData->getTableName();
            $createSqls = array_merge($createSqls, $platform->getCreateTableSQL(
                $toSchema->getTable($tableName),
                AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
            ));
        }
        foreach ($this->getRefTableNames($classNames, $em) as $refTableName) {
            $createSqls = array_merge($createSqls, $platform->getCreateTableSQL(
                $toSchema->getTable($refTableName),
                AbstractPlatform::CREATE_INDEXES | AbstractPlatform::CREATE_FOREIGNKEYS
            ));
        }
        $em->getConnection()->exec(implode(';', $createSqls));
    }

    /**
     * @param array $classNames
     * @param EntityManager $em
     * @throws DBALException
     * @throws SchemaException
     */
    public function dropTables(array $classNames, EntityManager $em)
    {
        $platform = $em->getConnection()->getDatabasePlatform();
        $toSchema = (new OrmSchemaProvider($em))->createSchema();
        $dropSqls = [];
        foreach ($this->getRefTableNames($classNames, $em) as $refTableName) {
            $dropSqls[] = $platform->getDropTableSQL($toSchema->getTable($refTableName));
        }

        foreach ($classNames as $className) {
            $metaData = $em->getClassMetadata($className);
            $tableName = $metaData->getTableName();
            $dropSqls[] = $platform->getDropTableSQL($toSchema->getTable($tableName));
        }
        $em->getConnection()->exec(implode(';', $dropSqls));
    }

    public function getRefTableNames(array $classNames, EntityManager $em): array
    {
        $result = [];
        foreach ($classNames as $className) {
            $metaData = $em->getClassMetadata($className);

            $associationMappings = $metaData->getAssociationMappings();
            foreach ($associationMappings as $fieldName => $associationMapping) {
                if (isset($associationMapping['joinTable'])) {
                    $result[] = $associationMapping['joinTable']['name'];
                }
            }
        }
        return $result;
    }
}
