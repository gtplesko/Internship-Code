<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/11/2017
 * Time: 2:15 PM
 */

namespace Tas\Tests\ReservEd\Proficiency;


use Tas\ReservEd\Proficiency\Proficiency;
use Tas\ReservEd\Proficiency\ProficiencyManager;
use Tas\Tests\ReservEd\ReservEdManagerTestCase;

class ProficiencyManagerTest extends ReservEdManagerTestCase {
  /**
   * @var ProficiencyManager
   */
  protected $proficiencyManager;
  public function setUp() {
    parent::setUp();
    $this->proficiencyManager = new ProficiencyManager($this->db);
  }
  public function testSaveProficiency(){
    $this->assertInstanceOf(ProficiencyManager::class, $this->proficiencyManager);

    $proficiency = new Proficiency();
    $proficiency->prof_code = 'JUG';
    $proficiency->title = 'Juggling';
    $proficiency->center_code = 'FACILITIES';
    $proficiency->inactive_ind = 'N';
    $proficiency->sort_order = 18;

    $this->proficiencyManager->saveProficiency($proficiency);

    $proficiency2 = $this->proficiencyManager->getProficiency('JUG');
    $this->assertEquals('JUG', $proficiency2->prof_code);

    $this->assertEntityEquals($proficiency, $proficiency2);
  }
  public function testBulkSave(){
    $proficiencies = $this->getYamlScenario('bulk_import_proficiencies');
    $this->proficiencyManager->saveProficiencies($this->proficiencyManager->arrayToProficiencies($proficiencies));
    foreach ($proficiencies as $proficiency_data){
      $proficiency = $this->proficiencyManager->getProficiency($proficiency_data['prof_code']);

      $this->assertEntityEqualsArray($proficiency_data, $proficiency);
    }
  }

}