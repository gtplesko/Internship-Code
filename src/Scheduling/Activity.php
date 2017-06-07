<?php

namespace Tas\ReservEd\Scheduling;
/**
 * Class Activity
 * @Entity
 * @Table (name="res_activities")
 * @package Tas\ReservEd\Activity
 */
class Activity {
  /**
   * @Id
   * @Column (type="integer", name="activity_id")
   * @GeneratedValue(strategy="SEQUENCE")
   * @SequenceGenerator(sequenceName="zseq_res_activity_id", initialValue=1)
   */
  public $activity_id;

  /**
   * @Column (name="event_id", type="integer")
   */
  public $event_id;

  /**
   * @Column (name="activity_type", length=30)
   */
  public $activity_type;

  /**
   * @Column (length=200)
   */
  public $title;

  /**
   * @Column (name="req_capacity", type="integer")
   */
  public $req_capacity;

  /**
   * @Column (type="date")
   */
  public $start_date;

  /**
   * @Column (type="date")
   */
  public $end_date;

  public function __construct($event_id = NULL, $title = NULL, $start = NULL, $end = NULL) {
    $this->event_id = $event_id;
    $this->title = $title;
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
  }
}