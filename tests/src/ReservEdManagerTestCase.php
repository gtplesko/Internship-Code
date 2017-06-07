<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/11/2017
 * Time: 1:38 PM
 */

namespace Tas\Tests\ReservEd;


use Symfony\Component\Yaml\Yaml;
use Tas\Tests\Core\Database\DoctrineAppTestCase;

class ReservEdManagerTestCase extends DoctrineAppTestCase {

  public function getYamlScenario($fileName) {
    $scenario = Yaml::parse($this->getScenariosDir()."/$fileName.yml");
    return $scenario;
  }
  public function getScenariosDir() {
    return dirname(__DIR__).'/scenarios';
  }

  public function assertEntityEquals($expected, $test) {
    foreach (get_class_vars(get_class($expected)) as $property => $_) {
      $this->assertEquals($expected->$property, $test->$property);
    }
  }
  public function assertEntityEqualsArray(array $expected, $test) {
    foreach ($expected as $property => $value) {
      $this->assertEquals($value, $test->$property);
    }
  }
}
