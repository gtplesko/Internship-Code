<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/12/2017
 * Time: 3:45 PM
 */

namespace Tas\Tests\ReservEd\Space;


use PHPUnit\Framework\TestCase;
use Tas\ReservEd\Space\Space;

class SpaceTest extends \PHPUnit_Framework_TestCase {

  public function testGenSpaceCode() {
    $this->assertEquals("LIB_2612", Space::generateSpaceCode("LIB",'2',612));
  }
}