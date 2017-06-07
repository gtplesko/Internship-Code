<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/19/2017
 * Time: 11:07 AM
 */

namespace Tas\ReservEd\Space;


/**
 * Class Campus
 * @Entity
 * @Table (name="STVCAMP")
 * @package Tas\ReservEd\Space
 */
class Campus {

  /**
   * @Id
   * @Column (name="STVCAMP_CODE", length=3)
   */
  public $campus_code;

  /**
   * @Column (name="STVCAMP_DESC", length=30)
   */
  public $description;
}