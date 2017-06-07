<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/24/2017
 * Time: 11:34 AM
 */

namespace Tas\Tests\ReservEd\Center;


use Tas\ReservEd\Center\Center;
use Tas\ReservEd\Center\CenterManager;
use Tas\Tests\ReservEd\ReservEdManagerTestCase;

class CenterManagerTest extends ReservEdManagerTestCase {
  /**
   * @var CenterManager
   */
  protected $centerManager;

  public function setUp() {
    parent::setUp();
    $this->centerManager = new CenterManager($this->db);
  }

  public function testSaveCenter(){
    $this->assertInstanceOf(CenterManager::class, $this->centerManager);

    $center = new Center();
    $center->center_code = "FUN_CENTER";
    $center->title="REALLY COOL FUN PEOPLE ZONE";
    $center->status_ind="A";

    $this->centerManager->saveCenter($center);

    $center2 = $this->centerManager->getCenter('FUN_CENTER');

    $this->assertEntityEquals($center, $center2);
  }

  public function testSaveRights() {
    $centerManger = $this->centerManager;
    $rights = [
      'write',
      'read',
      'execute',
      'dance',
    ];
    $group_name = 'testy_super_admin';
    $centerManger->saveRights('FACILITIES', $group_name, $rights);
    $grants = $centerManger->getGrants($group_name);
    $granted_rights = array_map(function($grant){
      return $grant->right;
    },$grants);
    foreach ($rights as $right) {
      $this->assertContains($right, $granted_rights);
    }
  }

  public function testUserRights() {
    $groups_rights = [
      'The ShawShank-Redmendtion' => ['poster','spoon'],
      'Live Calf Brither' => ['hay','milk'],
      'Hack-e-sackers Anonymous' => ['rainbow-kik', 'probably-like-up', 'sql-injections\' or 1=1 --'],
    ];
    $centerManager = $this->centerManager;
    $center = new Center();
    $center->center_code = 'FACILITIES';
    foreach ($groups_rights as $group_name => $rights) {
      $centerManager->saveRights($center->center_code, $group_name, $rights);
    }
    $rights = $centerManager->userRights($center,array_keys($groups_rights));
    $wanted_rights = array_reduce(array_values($groups_rights), 'array_merge', []);
    foreach ($wanted_rights as $right) {
      $this->assertContains($right, $rights);
    }
  }
}