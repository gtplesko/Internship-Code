<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/5/2017
 * Time: 2:01 PM
 */

namespace Tas\ReservEd\Space;


use Tas\ReservEd\Manager;
use Tas\Core\Model\DatabaseInterface;

class SpaceManager extends Manager {
  public function __construct(DatabaseInterface $db) {
    parent::setUp($db);
  }
  /**
   * Saves space object to database
   * @param Space $space
   * @param bool $flush
   * @return string the space_code of the saved space
   */
  public function saveSpace(Space &$space, $flush=TRUE){
    $space = static::$entityManager->merge($space);
    static::$entityManager->persist($space);
    if ($flush) {
      static::$entityManager->flush();
    }
    return $space->space_code;
  }
  /**
   * Retrieves space object from a database via the spaceCode
   * @param string $spaceCode
   * @param bool $detach
   * @return Space
   */
  public function getSpace($space_code, $detach=FALSE){
    $space = static::$entityManager->find(Space::class, $space_code);
    if (!$space) {
      $space = new Space();
    }
    else if ($detach) {
      static::$entityManager->detach($space);
    }
    return $space;
  }

  /**
   * @param Space[] $spaces
   */
  public function saveSpaces(array $spaces) {
    foreach ($spaces as $space) {
      $this->saveSpace($space, FALSE);
    }
    static::$entityManager->flush();
  }

  /**
   * @param array $properties
   * @return Space[]
   */
  public function arrayToSpaces(array $properties) {
    return static::$db->objectFactory(Space::class, $properties);
  }

  public function getAllCampuses() {
    return static::$entityManager->getRepository(Campus::class)->findBy([],['campus_code'=>'ASC']);
  }

  public function clear() {
    static::$entityManager->clear();
  }
}