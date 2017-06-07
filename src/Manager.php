<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/10/2017
 * Time: 11:49 AM
 */

namespace Tas\ReservEd;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Tas\Core\Model\DatabaseInterface;

class Manager {
  /** @var \Tas\Core\Model\DoctrineApp */
  protected static $db;

  /** @var  EntityManager */
  protected static $entityManager;

  protected static function setUp(DatabaseInterface $db) {
    if (static::$db == NULL) {
      static::$db = $db;
    }
    if(static::$entityManager == NULL) {
      $entity_config = Setup::createAnnotationMetadataConfiguration([__DIR__]);
      static::$entityManager = EntityManager::create(static::$db->connection,$entity_config);
    }
  }
}
