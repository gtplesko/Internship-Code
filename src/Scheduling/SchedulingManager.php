<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/10/2017
 * Time: 2:11 PM
 */

namespace Tas\ReservEd\Scheduling;


use Tas\Core\Model\DatabaseInterface;
use Tas\ReservEd\Manager;
use Tas\ReservEd\ScheduleEntry;

class SchedulingManager extends Manager {

  public function __construct(DatabaseInterface $db) {
    parent::setUp($db);
  }


  //Activity Manager
  /**
   * @param Activity $activity
   * @return mixed
   */
  public function saveActivity(Activity &$activity, $flush=TRUE) {
    $activity = static::$entityManager->merge($activity);
    static::$entityManager->persist($activity);
    if($flush) {
      static::$entityManager->flush();
    }
    return $activity->activity_id;
  }

  /**
   * @param $activity_id
   * @param bool $detach
   * @return Activity
   */
  public function getActivity($activity_id, $detach=FALSE) {
    $activity = static::$entityManager->find(Activity::class, $activity_id);
    if (!$activity) {
      $activity = new Activity();
    }
    else if ($detach) {
      static::$entityManager->detach($activity);
    }
    return $activity;
  }

  /**
   * @param array $activities
   */
  public function saveActivities(array &$activities) {
    foreach ($activities as $key=>$val) {
      $this->saveActivity($activities[$key], FALSE);
    }
    static::$entityManager->flush();
  }

  /**
   * @param array $activities
   * @return Activity[]
   */
  public function arrayToActivities(array $activities){
    return static::$db->objectFactory(Activity::class, $activities);
  }

  //Booking Manager
  /**
   * @param Booking $booking
   * @return mixed
   */
  public function saveBooking(Booking &$booking, $flush=TRUE) {
    static::$entityManager->merge($booking);
    static::$entityManager->persist($booking);
    if($flush) {
      static::$entityManager->flush();
    }
    return $booking->booking_id;
  }

  /**
   * @param integer $booking_id
   * @return null|object
   */
  public function getBooking($booking_id, $detach=FALSE) {
    $booking = static::$entityManager->find(Booking::class, $booking_id);
    if (!$booking) {
      $booking = new Booking();
    }
    else if ($detach) {
      static::$entityManager->detach($booking);
    }
    return $booking;
  }
  /**
   * @param array $bookings
   */
  public function saveBookings(array $bookings) {
    foreach ($bookings as $booking) {
      $this->saveBooking($booking, false);
    }
    static::$entityManager->flush();
  }
  /**
   * @param array $bookings
   * @return Booking[]
   */
  public function arrayToBookings(array $bookings){
    return static::$db->objectFactory(Booking::class, $bookings);
  }

  public function getScheduleForSpaceGroup($center_code, $space_group_code, $start_date, $end_date) {
    $sql = "SELECT 
              event_id, 
              event_title, 
              activity_id, 
              activity_title, 
              space_code, 
              start_time as start_date, 
              end_time as end_date
            FROM 
              ods_res_booking_calendar
            WHERE
              center_code = :center_code 
              AND grouping_code = :grouping_code
              AND (
                ( 
                  start_time >= :start_date 
                  AND 
                  (start_time <= :end_date OR end_time <= :end_date) 
                ) 
                OR 
                (start_time <= :start_date AND end_time > :start_date) 
              )";
    $parms = [
      'center_code'=>$center_code,
      'grouping_code'=>$space_group_code,
      'start_date'=> $start_date->format('Y-m-d H:i:s'),
      'end_date'=> $end_date->format('Y-m-d H:i:s'),
    ];
    $rows = static::$db->queryToObjects(ScheduleEntry::class, $sql, $parms);
    $sql = "SELECT space_code FROM res_spaces WHERE center_code = :center_code AND grouping_code = :grouping_code";
    $parms = [
      'center_code'=>$center_code,
      'grouping_code'=>$space_group_code,
    ];
    $spaces = static::$db->queryLower($sql, $parms);
    $groupedEntries = [];
    foreach ($spaces as $row){
      $groupedEntries[$row['space_code']] = [];
    }
    foreach ($rows as $entryObject){
      $groupedEntries[$entryObject->space_code][] = $entryObject;
    }
    return $groupedEntries;
  }

  //Event Manager
  /*
   *  Event:
   *  Something that has a definable start and end date that ties together
   *  multiple activities. Think about conference events where one event can have
   *  multiple meeting spaces.  This is the non-academic corollary to offerings.
   */
  /**
   * @param Event $event
   */
  public function saveEvent(Event &$event, $flush=TRUE) {
    static::$entityManager->merge($event);
    static::$entityManager->persist($event);
    if ($flush) {
      static::$entityManager->flush();
    }
    return $event->event_id;
  }

  /**
   * @param $event_id
   * @param bool $detach
   * @return Event
   */
  public function getEvent($event_id, $detach=FALSE) {
    $event = static::$entityManager->find(Event::class, $event_id);
    if (!$event) {
      $event = new Event();
    }
    else if ($detach) {
      static::$entityManager->detach($event);
    }
    return $event;
  }

  /**
   * @param Event[] $events
   */
  public function saveEvents(array &$events) {
    foreach($events as $key=>$val){
      $this->saveEvent($events[$key], FALSE);
    }
    static::$entityManager->flush();
  }
  /**
   * @param array $properties
   * @return Event[]
   */
  public function arrayToEvents(array $properties) {
    return static::$db->objectFactory(Event::class, $properties);
  }
}