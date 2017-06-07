<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/5/2017
 * Time: 1:55 PM
 */

namespace Tas\ReservEd\Space;

/**
 * Class Space
 * @Entity
 * @Table (name="res_spaces")
 * @package Tas\ReservEd\Space
 */
class Space {

  /**
   * example : "LIB 2612"
   * @Id
   * @Column (name="space_code", length=30)
   */
  public $space_code;
  /**
   * The Display name for the space
   * @Column (name="title", length=100)
   */
  public $title;
  /**
   * @Column (name="campus_code", length=30)
   */
  public $campus_code = 'OLY';
  /**
   * @Column (name="building_code", length=30)
   */
  public $building_code;

  /**
   * @Column (length=30)
   */
  public $center_code;

  /**
   * @Column
   */
  public $grouping_code;

  /**
   * @Column (name="floor_number", length=2)
   */
  public $floor_number;
  /**
   * @Column (name="space_type", length=30)
   */
  public $space_type;

  /**
   * @Column
   */
  public $capacity;

  /**
   * @Column (length=30)
   */
  public $default_config;

  /**
   * @Column (name="inactive_ind")
   */
  public $inactive_ind;

  /**
   * @Column
   */
  public $external_id;

  public static function generateSpaceCode($building_code, $floor, $room_number) {
    return "{$building_code}_{$floor}{$room_number}";
  }
}