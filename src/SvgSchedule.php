<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/15/2017
 * Time: 2:02 PM
 */

namespace Tas\ReservEd;


class SvgSchedule {

  const VIEWBOX_WIDTH = 1000;
  const VIEWBOX_HEIGHT = 1000;


  const HOUR_LENGTH = 60;
  const DAY_LENGTH = self::HOUR_LENGTH * 24;
  const WEEK_LENGTH = self::DAY_LENGTH * 7;
  protected $markingsMade = FALSE;
  protected $timespan;
  /**
   * @param \DateTime $from
   * @param \DateTime $to
   */
  public function setDateRange(\DateTime $from, \DateTime $to){
    $this->timespan = Timespan::create($from, $to);
  }


  public function svgSchedule(array $entries) {
  }

  public function svgFromEntries(array $entries, $svgMaker='makeWeekBlockFromEntry'){
    $viewingDuration = $this->timespan->duration;
    $rects = [];
    foreach($entries as $entry){
      $rects[] = $this->$svgMaker($entry, $viewingDuration,$this->timespan->beginning);
    }
    $svgHeader = $this->makeSvgHeader(static::VIEWBOX_WIDTH,static::VIEWBOX_HEIGHT);
    $svgBackground = $this->svgBackground(self::DAY_LENGTH, static::VIEWBOX_WIDTH,static::VIEWBOX_HEIGHT);
    return $svgHeader .  implode('', $rects) . $svgBackground . '</svg>';
  }


  public function calculateScale($duration, $scale) {
    return (1/$duration) * $scale;
  }

  public function calculateBoxLocation($entryDuration, $startDuration, $scale) {
    $width = $entryDuration * $scale;
    $start = $startDuration * $scale;
    return [$width, $start];
  }

  protected function makeSvgHeader($width, $height) {
    return "<svg height=\"50\" width=\"100%\" viewBox=\"0 0 $width $height\" preserveAspectRatio=\"none\">" ;
  }

  public function svgBackground($duration, $width=self::VIEWBOX_WIDTH, $height=self::VIEWBOX_HEIGHT){
    if(!$this->markingsMade) {
      $markingDuration = 0;
      $largeMark = $height / 2;
      $smallMark = $height / 4;
      $svgString = '';
      if ($duration <= self::HOUR_LENGTH) {
        $markingDuration = 5; //every 5 minutes
        $timeFactor = $markingDuration;
      } elseif ($duration <= self::DAY_LENGTH) {
        $markingDuration = self::HOUR_LENGTH / 2; //every hour with half hours half height
        $timeFactor = self::HOUR_LENGTH;
      } elseif ($duration <= self::WEEK_LENGTH) {
        $markingDuration = self::DAY_LENGTH / 4; //every day with 6 hours half height
        $timeFactor = self::DAY_LENGTH;
      } else {
        $markingDuration = self::WEEK_LENGTH; //Weeks
        $timeFactor = self::WEEK_LENGTH;
      }
      $scale = $this->calculateScale($duration, $width);
      foreach (range(0, $duration, $markingDuration) as $i) {
        if ($i % $timeFactor == 0) {
          $svgString .= $this->makeLine($i * $scale, 0, $i * $scale, $largeMark);
        } else {
          $svgString .= $this->makeLine($i * $scale, 0, $i * $scale, $smallMark);
        }

      }
      $this->markingsMade=TRUE;
      return "<g id='timeMarkings'> $svgString </g>";
    }else{
      return '<use xlink:href="#timeMarkings"></use>';
    }
  }
  protected function makeWeekBlockFromEntry($entry, $viewingDuration, $viewingStart, $width=self::VIEWBOX_WIDTH, $height=self::VIEWBOX_HEIGHT) {
    return $this->makeBlockFromEntry($entry, $viewingDuration/7, $viewingStart, $width, $height/7);
  }
  protected function makeBlockFromEntry($entry, $viewingDuration, $viewingStart, $width=self::VIEWBOX_WIDTH, $height=self::VIEWBOX_HEIGHT){
    $entryDuration = Timespan::create($entry->start_date, $entry->end_date)->duration;
    $startDuration = Timespan::create($viewingStart, $entry->start_date)->duration;
    $scale = $this->calculateScale($viewingDuration, $width);
    list($boxWidth, $boxStart) = $this->calculateBoxLocation($entryDuration, $startDuration, $scale);
    return $this->makeBox($boxStart%$width, floor($boxStart/$width) * $height, $boxWidth, $height, $entry->event_id );
  }
  protected function makeBox($boxStartX, $boxStartY, $boxWidth, $boxHeight, $id){
    return "<rect class=\"svg-schedule-entry\" x=\"$boxStartX\" y=\"$boxStartY\" width=\"$boxWidth\" height=\"$boxHeight\" id=\"$id\"/>";
  }

  protected function makeLine($x1,$y1, $x2,$y2) {
    return "<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' style='stroke:rgb(160,160,160);stroke-width: 1; stroke-opacity: .6;  '></line>";
  }
}