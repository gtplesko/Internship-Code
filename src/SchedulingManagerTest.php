<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/10/2017
 * Time: 2:13 PM
 */

namespace Tas\Tests\ReservEd;

use Tas\ReservEd\Scheduling\Activity;
use Tas\ReservEd\Scheduling\Booking;
use Tas\ReservEd\Scheduling\Event;
use Tas\ReservEd\Scheduling\SchedulingManager;
use Tas\Tests\ReservEd\ReservEdManagerTestCase;

class SchedulingManagerTest extends ReservEdManagerTestCase {
  /**
   * @var SchedulingManager
   */
  protected $schedulingManager;
  public function setUp() {
    parent::setUp();
    $this->schedulingManager = new SchedulingManager($this->db);
  }

  public function testSaveBooking() {

    $booking = new Booking();
    //$booking->booking_id = 2;
    $booking->activity_id = 323;
    $booking->space_code = 'eighteen';
    $booking->start_time = new \DateTime('10/14/1991');
    $booking->end_time = new \DateTime( '4/11/2017');

    $id = $this->schedulingManager->saveBooking($booking);

    $booking2 = $this->schedulingManager->getBooking($id);
    $this->assertEquals($id, $booking2->booking_id);

    $this->assertEntityEquals($booking, $booking2);
  }
  public function testBulkSave() {
    $booking_data = $this->getYamlScenario('bulk_import_bookings');
    $bookings = $this->schedulingManager->arrayToBookings($booking_data);
    $this->schedulingManager->saveBookings($bookings);
    foreach ($bookings as $booking){
     $booking2 = $this->schedulingManager->getBooking($booking->booking_id);
      $this->assertEntityEquals($booking, $booking2);
    }

  }

  public function testSaveEvent(){

    $event = new Event();
    $event->event_type = 'Class';
    $event->event_source_code = 'Bob Ross';
    $event->title = 'The Joy of Painting';
    $event->start_date = new \DateTime('01/11/1983');
    $event->end_date = new \DateTime('05/17/1994');
    $event->owner_pidm = 12055;
    $event->fund_code = 'PBS';
    $event->orgn_code = 'Public Broadcast Service';
    $event->status_ind = 'Y';
    $event->user_name = 'Weissr';
    $event->activity_date = new \DateTime('01/11/1983');


    $event_id = $this->schedulingManager->saveEvent($event);

    $event2 = $this->schedulingManager->getEvent($event_id);
    $this->assertEquals($event_id, $event2->event_id);

    $this->assertEntityEquals($event, $event2);
  }

  public function testBulkEventSave(){
    $event_props = $this->getYamlScenario('bulk_import_events');
    $events = $this->schedulingManager->arrayToEvents($event_props);
    $this->schedulingManager->saveEvents($events);
    foreach ($events as $event){
      $event2 = $this->schedulingManager->getEvent($event->event_id);
      $this->assertEntityEquals($event, $event2);
    }

  }

  public function testSaveActivity(){

    $activity = new Activity();
    $activity->event_id = 1337;
    $activity->activity_type = 'Olympics';
    $activity->title = 'We are fun people.';

    $this->schedulingManager->saveActivity($activity);
    $activity_id = $activity->activity_id;
    $activity2 = $this->schedulingManager->getActivity($activity_id);
    $this->assertEquals($activity_id, $activity2->activity_id);

    $this->assertEntityEquals($activity, $activity2);
  }

  public function testBulkActivitySave(){
    $activity_props = $this->getYamlScenario('bulk_import_activities');
    $activities = $this->schedulingManager->arrayToActivities($activity_props);
    $this->schedulingManager->saveActivities($activities);
    foreach ($activities as $activity){
      $activity2 = $this->schedulingManager->getActivity($activity->activity_id);
      $this->assertEntityEquals($activity, $activity2);
    }

  }
}