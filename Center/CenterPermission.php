<?php

namespace Tas\ReservEd\Center;

/**
 * Class CenterPermission
 * @package Tas\ReservEd\Center
 * @Entity
 * @Table (name="res_center_perm")
 */
class CenterPermission {
  /**
   * @Id
   * @Column
   */
  public $center_code;
  /**
   * @Id
   * @Column
   */
  public $grantee;
  /**
   * @Id
   * @Column
   */
  public $right;

  public function __construct($center_code, $grantee, $right) {
    $this->center_code = $center_code;
    $this->grantee = $grantee;
    $this->right = $right;
  }
}