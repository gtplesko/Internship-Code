<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/11/2017
 * Time: 10:55 AM
 */

namespace Tas\ReservEd\Scheduling;

/**
 * Class Event
 * @Entity
 * @package Tas\ReservEd\Event
 * @Table (name="res_events")
 */
class Event {
  /**
   * @Id
   * @Column (name="event_id", type="integer")
   * @GeneratedValue(strategy="SEQUENCE")
   * @SequenceGenerator(sequenceName="zseq_res_event_id", initialValue=1)
   */
  public $event_id;
  /**
   * @Column (length=30)
   */
  public $event_type;
  /**
   * @Column (length=30)
   */
  public $event_source_code;
  /**
   * @Column (length=100)
   */
  public $title;
  /**
   * @Column (type="date", nullable=false)
   */
  public $start_date;
  /**
   * @Column (type="date", nullable=false)
   */
  public $end_date;
  /**
   * @Column (type="integer")
   */
  public $owner_pidm;
  /**
   * @Column (length=30)
   */
  public $fund_code;
  /**
   * @Column (length=30)
   */
  public $orgn_code;
  /**
   * @Column (length=1)
   */
  public $status_ind;
  /**
   * @Column (length=30)
   */
  public $user_name;
  /**
   * @Column (type="date")
   */
  public $activity_date;

  public function __construct($title = NULL, $type = NULL, $start = NULL, $end = NULL) {
    $this->title = $title;
    $this->event_type = $type;
    if ($start instanceof \DateTime){
      $this->start_date = $start;
    }else{
      if($start != NULL){
        $this->start_date = new \DateTime($start);
      }
    }

    if ($end instanceof \DateTime){
      $this->end_date = $end;
    }else{
      if($end != NULL){
        $this->end_date = new \DateTime($end);
      }
    }
  }

  public function __wakeup() {
    $this->start_date = new \DateTime($this->start_date);
    $this->end_date = new \DateTime($this->end_date);
    // activity_date is nullable
    if ($this->activity_date != NULL) {
      $this->activity_date = new \DateTime($this->activity_date);
    }
  }
}