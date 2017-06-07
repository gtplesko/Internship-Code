<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/24/2017
 * Time: 11:25 AM
 */

namespace Tas\ReservEd\Center;


use Tas\Core\Model\DatabaseInterface;
use Tas\ReservEd\Manager;

class CenterManager extends Manager {

  public function __construct(DatabaseInterface $db) {
    parent::setUp($db);
  }

  /**
   * @param Center $center
   */
  public function saveCenter(Center $center, $flush=TRUE) {
    $center = static::$entityManager->merge($center);
    static::$entityManager->persist($center);
    if ($flush) {
      static::$entityManager->flush();
    }
    return $center->center_code;
  }

  /**
   * @param $center_code
   * @param bool $detach
   * @return null|object
   */
  public function getCenter($center_code, $detach=FALSE) {
    $center = static::$entityManager->find(Center::class, $center_code);
    if (!$center) {
      $center = new Center();
    }
    else if ($detach) {
      static::$entityManager->detach($center);
    }
    return $center;
  }

  /**
   * @return Center[]
   */
  public function getAllCenters(){
    return static::$entityManager->getRepository(Center::class)->findBy([], ['center_code'=>'ASC']);
  }

  /**
   * @param $center_code
   * @param $group_name
   * @param array $rights
   */
  public function saveRights($center_code, $group_name, array $rights) {
    foreach ($rights as $right) {
      $grant = new CenterPermission($center_code, $group_name, $right);
      static::$entityManager->persist($grant);
    }
    static::$entityManager->flush();
  }

  /**
   * @param $group_name
   * @return array
   */
  public function getGrants($group_name){
    $result = static::$entityManager
      ->getRepository(CenterPermission::class)
      ->findBy(['grantee' => $group_name]);
    return $result;
  }

  /**
   * @param $center_code
   * @param array $groups
   * @return array
   */
  public function userRights(Center $center, array $groups) {
    $requirements = [
      'center_code' => $center->center_code,
      'grantee'     => $groups,
    ];
    $result = static::$entityManager
                ->getRepository(CenterPermission::class)
                ->findBy($requirements);

    $rights = array_map(function($perm){ return $perm->right; },$result);
    $center->rights = array_unique($rights);
    return $center->rights;
  }

  /**
   * @param array $grants
   */
  public function importGrants(array $grants) {
    foreach ($grants as $grant) {
      $grant = new CenterPermission($grant['center_code'], $grant['group_name'], $grant['right']);
      static::$entityManager->persist($grant);
    }
    static::$entityManager->flush();
  }
}