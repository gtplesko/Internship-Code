<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/15/2017
 * Time: 2:15 PM
 */

namespace Tas\Tests\ReservEd;


use Tas\ReservEd\ScheduleEntry;
use Tas\ReservEd\SvgSchedule;

class SvgScheduleTest extends \PHPUnit_Framework_TestCase {

  function testSvg(){
    $date1 = new \Datetime('2001-03-01 00:00:00.000000');
    $date2 =  new \Datetime('2001-03-01 02:00:00.000000');
    $date3 =  new \Datetime('2001-03-01 04:00:00.000000');

    $svgSchedule = new SvgSchedule();
    $svgSchedule->setDateRange($date1, $date3);
    $entry = new ScheduleEntry();
    $entry->event_title = 'eventarino';
    $entry->event_id = 8675309;
    $entry->activity_label = 'activitiarino';
    $entry->activity_id = 54;
    $entry->setup_start = $date1;
    $entry->start_date = $date1;
    $entry->end_date = $date2;
    $entry->teardown_end = $date2;

    $svg = $svgSchedule->svgFromEntries([$entry]);
    $svgXml = simplexml_load_string($svg);
    $this->assertFalse($svgXml === FALSE);//asserting that it is valid xml

    //$entries = $svgXml->xpath('*[@class="svg-schedule-entry"]');
    //$this->assertTrue(explode(" ", $svgXml->attributes()['viewBox'])[2]/2.0 == ($entries[0]->attributes()['width']));
  }
}