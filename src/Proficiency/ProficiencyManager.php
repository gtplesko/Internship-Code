<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/11/2017
 * Time: 2:08 PM
 */

namespace Tas\ReservEd\Proficiency;


use Tas\Core\Model\DatabaseInterface;
use Tas\ReservEd\Center\Center;
use Tas\ReservEd\Manager;

class ProficiencyManager extends Manager {
  public function __construct(DatabaseInterface $db) {
    parent::setUp($db);
  }

  /**
   * Saves proficiency object to database
   * @param Proficiency $proficiency
   * @param bool $flush
   * @return string the prof_code of the saved proficiency
   */
  public function saveProficiency(Proficiency $proficiency, $flush=TRUE) {
    $proficiency = static::$entityManager->merge($proficiency);
    static::$entityManager->persist($proficiency);
    if ($flush) {
      static::$entityManager->flush();
    }
    return $proficiency->prof_code;
  }

  /**
   * Retrieves proficiency object from a database via the prof_code
   * @param string $prof_code
   * @param bool $detach
   * @return Proficiency
   */
  public function getProficiency($prof_code, $detach=FALSE) {
    $proficiency = static::$entityManager->find(Proficiency::class, $prof_code);
    if (!$proficiency) {
      $proficiency = new Proficiency();
    }
    else if ($detach) {
      static::$entityManager->detach($proficiency);
    }
    return $proficiency;
  }

  /**
   * @param Proficiency[] $proficiencies
   */
  public function saveProficiencies(array $proficiencies) {
    foreach ($proficiencies as $proficiency) {
      $this->saveProficiency($proficiency, FALSE);
    }
    static::$entityManager->flush();
  }

  /**
   * @param array $properties
   * @return Proficiency[]
   */
  public function arrayToProficiencies(array $properties) {
    return static::$db->objectFactory(Proficiency::class,$properties);
  }
  public function getAllCenters() {
    return static::$entityManager->getRepository(Center::class)->findBy([],['center_code'=>'ASC']);
  }
}