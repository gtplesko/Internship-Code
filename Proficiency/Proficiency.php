<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/11/2017
 * Time: 2:07 PM
 */

namespace Tas\ReservEd\Proficiency;

/**
 * Class Proficiency
 * @Entity
 * @Table (name="res_proficiencies")
 * @package Tas\ReservEd\Proficiency
 */
class Proficiency {
  /**
   * @Id
   * @Column (length=30)
   */
  public $prof_code;
  /**
   * @Column (length=100)
   */
  public $title;
  /**
   * @Column (length=30)
   */
  public $center_code;
  /**
   * @Column (length=1)
   */
  public $inactive_ind = 'N';
  /**
   * @Column (type="integer")
   */
  public $sort_order;
}