<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/6/2017
 * Time: 10:36 AM
 */

namespace Tas\ReservEd\Scheduling;

/**
 * Class Scheduling
 *
 * @Entity
 * @Table (name="res_bookings")
 * @package Tas\ReservEd\Scheduling
 */
class Booking {
  /**
   * @Id
   * @Column (name="booking_id", type="integer").
   * @GeneratedValue(strategy="SEQUENCE")
   * @SequenceGenerator (sequenceName="zseq_res_booking_id", initialValue=1)
   */
  public $booking_id;

  /**
   * @Column (name="activity_id", nullable=false)
   */
  public $activity_id;

  /**
   * @Column (name="space_code", nullable=false, length=30)
   */
  public $space_code;

  /**
   * @Column (name="start_time", type="datetime")
   */
  public $start_time;

  /**
   * @Column (name="end_time", type="datetime")
   */
  public $end_time;

  public function __construct($activity_id = NULL, $space_code = NULL, $start = NULL, $end = NULL) {

    $this->activity_id = $activity_id;
    $this->space_code = $space_code;
    if ($start instanceof \DateTime){
      $this->start_time = $start;
    }else{
      if($start != NULL){
        $this->start_time = new \DateTime($start);
      }
    }

    if ($end instanceof \DateTime){
      $this->end_time = $end;
    }else{
      if($end != NULL){
        $this->end_time = new \DateTime($end);
      }
    }
  }

  public function __wakeup() {
    if ($this->start_time != NULL) {
      $this->start_time = new \DateTime($this->start_time);
    }

    if ($this->end_time != NULL) {
      $this->end_time = new \DateTime($this->end_time);
    }
  }

  /**
   * This Scheduling overlaps with that
   *
   * @param Booking $that
   * @return bool
   */
  public function overlaps($that) {
    return ($this->start_time < $that->end_time && $that->start_time < $this->end_time);
  }
  /**
   * This Scheduling is adjacent with that
   *
   * @param Booking $that
   * @return bool
   */
  public function adjacent($that) {
    return ($this->start_time == $that->end_time || $this->end_time == $that->start_time);
  }
  /**
   * This Scheduling contains another Scheduling, DateTime or string date
   *
   * @param $that
   * @return bool
   */
  public function contains($that) {
    if ($that instanceof Booking) {
      // passed another Scheduling
      return ($this->start_time <= $that->start_time && $that->end_time <= $this->end_time);
    } elseif ($that instanceof \DateTime) {
      return ($this->start_time <= $that && $that < $this->end_time);
    } else {
      $dt = new \DateTime($that);
      return ($this->start_time <= $dt && $dt < $this->end_time);
    }
  }


}