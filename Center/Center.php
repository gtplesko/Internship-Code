<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/24/2017
 * Time: 11:22 AM
 */

namespace Tas\ReservEd\Center;

/**
 * Class Center
 * @package Tas\ReservEd\Center
 * @Entity
 * @Table (name="res_centers")
 */
class Center {
  /**
   * @Id
   * @Column
   */
  public $center_code;
  /**
   * @Column
   */
  public $title;
  /**
   * @Column
   */
  public $status_ind;
  /**
   * @var array $roles
   */
  public $rights = [];
  /**
   * @param $group_name
   * @param $right
   */
  public function hasRight($right){
    return in_array($right, $this->rights);
  }
}