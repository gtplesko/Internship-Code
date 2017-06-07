<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/5/2017
 * Time: 2:27 PM
 */

namespace Tas\Tests\ReservEd\Space;


use Tas\ReservEd\Space\Space;
use Tas\ReservEd\Space\SpaceManager;
use Tas\Tests\ReservEd\ReservEdManagerTestCase;

class SpaceManagerTest extends ReservEdManagerTestCase {
  /**
   * @var SpaceManager
   */
  protected $spaceManager;

  public function setUp() {
    parent::setUp();
    $this->spaceManager = new SpaceManager($this->db);
  }

  public function testSaveSpace(){
    $this->assertInstanceOf(SpaceManager::class,$this->spaceManager);
    $space = new Space();
    $space->space_code = "LIB 2612";
    $space->title = "Library 2612";
    $space->campus_code = 'OLY';
    $space->building_code = 'LIB';
    $space->floor_number = '2';
    $space->space_type = 'COMP LAB';
    $space->grouping_code = 'Studio';
    $space->capacity = 25;
    $space->inactive_ind = 'n';
    $this->spaceManager->saveSpace($space);

    $this->spaceManager->clear();

    $space2 = $this->spaceManager->getSpace("LIB 2612");
    $this->assertEquals("LIB 2612", $space2->space_code);
    $this->assertEquals('Studio', $space2->grouping_code);

    $this->assertEntityEquals($space, $space2);
  }
  public function testBulkSave(){
    $spaces = $this->getYamlScenario('bulk_import_spaces');
    $this->spaceManager->saveSpaces($this->spaceManager->arrayToSpaces($spaces));
    foreach ($spaces as $space_data){
      $space = $this->spaceManager->getSpace($space_data['space_code']);

      $this->assertEntityEqualsArray($space_data, $space);
    }
  }
  public function testGetCampuses() {
    $this->assertNotEmpty($this->spaceManager->getAllCampuses());
  }
}