<?php
/**
 * Created by PhpStorm.
 * User: baileys
 * Date: 4/5/2017
 * Time: 1:54 PM
 */

namespace Tas\ReservEd;



/**
 * Class Timespan
 * @package Drupal\reserved
 *
 * @property string $beginning
 *   begining iso date/time for the shift.  When setting any \DateTime
 *   constructor can be used.
 * @property string $ending
 *   ending iso date/time for the shift.  When setting any value that can be
 *   passed to the DateTime constructor may be used.
 * @property-read string $calendar_date
 *   ISO representation of start date of calendar.
 * @property int $duration
 */
class Timespan implements \IteratorAggregate{
  /** @var  \DateTime */
  protected $_beginning;
  /** @var  \DateTime */
  protected $_ending;
  protected $_duration;

  public static $date_format = 'Y-m-d H:i';

  public function __construct($properties=array()) {
    if ($properties) foreach($properties as $property_name=>$value){
      $this->$property_name = $value;
    }
  }

  public function __get($name) {
    $value = NULL;
    switch ($name) {
      case 'beginning':
        $value = (is_object($this->_beginning)) ? $this->_beginning->format(static::$date_format) : NULL;
        break;
      case 'ending':
        if (is_object($this->_ending)) {
          $value = $this->_ending->format(static::$date_format);
        } else {
          $this->_calculateEnding();
          $value = (is_object($this->_ending)) ? $this->_ending->format(static::$date_format) : NULL;
        }
        break;
      case 'duration':
        if (isset($this->_duration)) {
          $value = $this->_duration;
        }
        elseif (isset($this->_beginning) && isset($this->_ending)) {
          $value = $this->minutes();
        }
        break;
      case 'calendar_date':
        if (isset($this->_beginning)) {
          $value = substr($this->_beginning->format(static::$date_format), 0, 10);
        }
        break;
    }
    return $value;
  }

  public function __set($name, $value) {
    if ($name == 'beginning' || $name == 'ending') {
      if (!$value instanceof \DateTime) {
        $value = (!empty($value)) ? new \DateTime($value) : NULL;
      }
      if ($name == 'beginning') {
        $this->_beginning = $value;
        $this->_calculateEnding();
      } else {
        $this->_ending = $value;
      }
    } elseif ($name == 'duration') {
      $this->_duration = $value;
      $this->_calculateEnding();
    }
    $this->_validate($name);
  }

  /**
   * Establishing iterator interface for object vars.
   */
  public function getIterator() {
    // Get  internal properties
    $data['calendar_date'] = $this->calendar_date;
    $data['beginning'] = $this->beginning;
    $data['ending'] = $this->ending;
    $data['duration'] = $this->duration;


    // Add normal properties
    // call_user_func is used to avoid capturing internal properties
    $data = array_merge($data, call_user_func('get_object_vars', $this));

    return new \ArrayIterator($data);

  }

  public function __clone() {
    if (is_object($this->_beginning)) {
      $this->_beginning = clone $this->_beginning;
    }
    if (is_object($this->_ending)) {
      $this->_ending = clone $this->_ending;
    }
  }

  /**
   * @return \DateTime
   */
  public function getBeginning() {
    return $this->_beginning;
  }

  /**
   * @return \DateTime
   */
  public function getEnding() {
    return $this->_ending;
  }

  public function getStartMin() {
    $cal_date = new \DateTime(substr($this->beginning, 0, 10));
    $interval = $this->_beginning->diff($cal_date);
    return $interval->h * 60 + $interval->i;
  }


  /**
   * Create a deep copy setting new beginning and ending dates
   *
   * @param $beginning
   * @param $ending
   * @return Timespan
   */
  public function copy($beginning, $ending) {
    $result = clone $this;
    $result->_beginning = (is_object($beginning)) ? clone $beginning : $beginning;
    $result->_ending = (is_object($ending)) ? clone $ending : $ending;
    return $result;
  }

  /**
   * Calculate and set ending from beginning and duration.
   */
  protected function _calculateEnding() {
    if (isset($this->_beginning) && isset($this->_duration)) {
      $beginning = $this->_beginning;
      $duration = (int)$this->_duration;
      $ending = clone $beginning;

      $interval = new \DateInterval("PT{$duration}M");
      $ending->add($interval);
      $this->_ending = $ending;
    }
  }

  private function _validate($last_value_set) {
    if (isset($this->_beginning) && isset($this->_ending) && $this->_beginning > $this->_ending) {
      // Invalid range start comes after beginning
      if ($last_value_set == 'ending') {
        $this->_beginning = $this->_ending;
      } else {
        $this->_ending = $this->_beginning;
      }
    }
  }

  public function __toString() {
    return '[' . $this->beginning . ', ' .
      $this->ending . ')';
  }

  /**
   * Create Timespan from two dates
   * @param $beginning
   * @param $ending
   * @return Timespan
   */
  public static function create($beginning, $ending) {
    return new static(array('beginning' => $beginning,
      'ending' => $ending));
  }

  /**
   * Create Timespan from start time and duration in minutes
   * @param $beginning
   * @param int $duration
   * @return Timespan
   */
  public static function createFromStartDuration($beginning, $duration) {
    $result = new static();
    $result->beginning = $beginning;
    $result->duration = $duration;
    return $result;
  }

  public static function compare($p1, $p2) {
    if ($p1->_beginning < $p2->_beginning) {
      return -1;
    } elseif ($p1->_beginning > $p2->_beginning) {
      return 1;
    } else {
      // beginnings are equal
      if ($p1->_ending == $p2->_ending) {
        return 0;
      } elseif ($p1->_ending < $p2->_ending) {
        return -1;
      } else {
        return 1;
      }
    }
  }

  public static function equals($p1, $p2) {
    if ($p1->_beginning == $p2->_beginning && $p1->_ending && $p2->_ending) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Return the center point of the timespan
   *
   * @return \DateTime
   */
  public function center() {
    $minutes = round($this->minutes() / 2);
    $interval = new \DateInterval("PT{$minutes}M");
    $result = clone $this->_beginning;
    $result->add($interval);
    return $result;
  }

  /**
   * This timespan contains another timespan, DateTime or string date
   *
   * @param $p1
   * @return bool
   */
  public function contains($p1) {
    if ($p1 instanceof Timespan) {
      // passed another Timespan
      if ($this->_beginning <= $p1->_beginning && $p1->_ending <= $this->_ending) {
        return TRUE;
      }
    } elseif ($p1 instanceof \DateTime) {
      if ($this->_beginning <= $p1 && $p1 < $this->_ending) {
        return TRUE;
      }
    } else {
      $dt = new \DateTime($p1);
      if ($this->_beginning <= $dt && $dt < $this->_ending) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * This timespan overlaps with p1
   *
   * @param Timespan $p1
   * @return bool
   */
  public function overlaps($p1) {
    if ($this->_beginning < $p1->_ending && $p1->_beginning < $this->_ending) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * This timespan is adjacent with p1
   *
   * @param Timespan $p1
   * @return bool
   */
  public function adjacent($p1) {
    if ($this->_beginning == $p1->_ending || $this->_ending == $p1->_beginning) {
      return TRUE;
    }
    return FALSE;
  }
  /**
   * Find any elements in arr1 that overlap with this Timespan
   *
   * @param Timespan[] $arr1
   * @return Timespan[]
   */
  public function matchOverlapping($arr1) {
    $result = array();
    foreach($arr1 as $shift) {
      if ($this->overlaps($shift)) {
        $result[] = $shift;
      }
    }
    return $result;
  }

  /**
   * Find all elements in arr1 that either overlap or are adjacent to this Timespan.
   *
   * @parm Timespan[] $arr1
   * @return Timespan[]
   */
  public function matchTouching($arr1) {
    $result = array();
    foreach($arr1 as $shift) {
      if ($this->overlaps($shift) || $this->adjacent($shift)) {
        $result[] = $shift;
      }
    }
    return $result;
  }

  /**
   * This timespan not only contains p1 but extends before and after
   *
   * @param $p1
   * @return bool
   */
  public function encloses($p1) {
    if ($this->_beginning < $p1->_beginning && $this->_ending > $p1->_ending) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Calculate the duration in minutes
   *
   * @return int
   */
  public function minutes() {
    if (isset($this->_duration)) {
      return $this->_duration;
    }
    if (isset($this->_ending)) {
      $interval = $this->_ending->diff($this->_beginning);
      return $interval->d * 1440 + $interval->h * 60 + $interval->i;
    }
  }

  //------------------------------ Set Operations ------------------------------------//
  /**
   * Return Timespan for all time in both this and that
   *
   * @param Timespan $p1
   * @return Timespan
   */
  public function intersect($p1) {
    if ($this->overlaps($p1)) {
      $beginning =  ($this->_beginning < $p1->_beginning) ? $p1->_beginning : $this->_beginning;
      $ending = ($this->_ending > $p1->_ending) ? $p1->_ending : $this->_ending;
      return $this->copy($beginning, $ending);
    }
  }

  /**
   * Return Timespans for all time in this but not in that
   *
   * @param Timespan $p1
   * @return Timespan[]
   */
  public function diff($p1) {
    /*  Scenario A              Scenario B             Scenario C
     *  [*********)              [*********)           [*********)
     *         [----)         [----)                      [----)
     *  [******)                   [*******)           [**)    [*)
     */
    $result = array();
    if ($this->_beginning < $p1->_beginning && $p1->_beginning < $this->_ending) {
      // B or C
      $result[] = $this->copy($this->_beginning, $p1->_beginning);
      if ($p1->_ending < $this->_ending) {
        // C
        $result[] = $this->copy($p1->_ending, $this->_ending);
      }
    } elseif ($this->_beginning < $p1->_ending && $p1->_ending < $this->_ending) {
      // A
      $result[] = $this->copy($p1->_ending, $this->_ending);
    } else {
      // no overlap
      $result[] = $this->copy($this->_beginning, $this->_ending);
    }
    return $result;
  }

  /**
   * Diff all elements of arr1 from this span
   * @param Timespan[] $arr1
   * @return Timespan[]
   */
  public function diffArray($arr1) {
    $result = array();
    $last_begin = $this->_beginning;
    Timespan::sortArray($arr1);
    for($i=0; $i<count($arr1); $i++) {
      $span = $arr1[$i];
      if(!$this->overlaps($span)) continue;
      if ($span->_beginning <= $last_begin && $span->_ending > $last_begin) {
        $last_begin = min($span->_ending, $this->_ending);
      } elseif ($span->_beginning > $last_begin) {
        $result[] = $this->copy($last_begin, min($span->_beginning, $this->_ending));
        $last_begin = $span->_ending;
      }
    }
    if ($last_begin < $this->_ending) {
      $result[] = $this->copy($last_begin, $this->_ending);
    }
    return $result;
  }

  /**
   * Return Timespan of all time in either this or that.
   * @param $p1
   * @return Timespan
   */
  public function union($p1) {
    if ($this->_beginning < $p1->_ending && $p1->_beginning < $this->_ending) {
      $beginning = ($this->_beginning < $p1->_beginning) ? $this->_beginning : $p1->_beginning;
      $ending = ($this->_ending > $p1->_ending) ? $this->_ending : $p1->_ending;
      return $this->copy($beginning, $ending);
    }
    return $this->copy($this->_beginning, $this->_ending);
  }

  /**
   * Split a Timespan by another Timespan or a DateTime
   *
   * @param $p1
   * @return Timespan[]
   */
  public function split($p1) {
    $result = array();
    $splitAt = array();
    if ($p1 instanceof Timespan) {
      if ($this->_beginning < $p1->_beginning && $p1->_beginning < $this->_ending) {
        $splitAt[] = $p1->_beginning;
      }
      if ($this->_beginning < $p1->_ending && $p1->_ending < $this->_ending) {
        $splitAt[] = $p1->_ending;
      }
    } else {
      if (! $p1 instanceof  \DateTime) {
        $p1 = new \DateTime($p1);
      }
      if ($this->_beginning < $p1 && $p1 < $this->_ending) {
        $splitAt[] = $p1;
      }
    }
    if (count($splitAt) == 1) {
      $result[] = $this->copy($this->_beginning, $splitAt[0]);
      $result[] = $this->copy($splitAt[0], $this->_ending);
    } elseif (count($splitAt) == 2) {
      $result[] = $this->copy($this->_beginning, $splitAt[0]);
      $result[] = $this->copy($splitAt[0], $splitAt[1]);
      $result[] = $this->copy($splitAt[1], $this->_ending);
    } else {
      $result[] = $this->copy($this->_beginning, $this->_ending);
    }
    return $result;
  }

  //------------------------ Set Operations on Arrays of Timespans  ----------------------//
  static public function printArray($arr1) {
    $result = "[";
    foreach($arr1 as $span) {
      $result .= (String) $span . ",\n";
    }
    $result .= "]";
    return $result;
  }

  /**
   * Return all members of $p1 that overlap with a member of $p2
   * @param Timespan[] $p1
   * @param Timespan[] $p2
   * @return Timespan[]
   */
  static public function overlappingArrays($p1, $p2) {
    $result = array();
    foreach($p1 as $tsa) {
      foreach($p2 as $tsb) {
        if ($tsa->overlaps($tsb)) {
          $result[] = $tsa;
          continue;
        }
      }
    }
    usort($result, array(static::class, 'compare'));
    return $result;
  }

  /**
   * Return any overlapping spans within array
   * @param Timespan[] $p1
   * @return Timespan[]
   */
  static public function getOverlapping($p1) {
    $result = array();
    $len = count($p1);
    for ($i=0; $i<$len-1; $i++) {
      for ($j=$i+1; $j<$len; $j++) {
        $a = $p1[$i];
        $b = $p1[$j];
        if ($a->overlaps($b)) {
          if (!in_array($a, $result))  $result[] = $a;
          if (!in_array($b, $result)) $result[] = $b;
        }
      }
    }
    usort($result, array(static::class, 'compare'));
    return $result;
  }



  /** Return the intersection of arrays p1 and p2
   * @param Timespan[] $p1
   * @param Timespan[] $p2
   * @return Timespan[]
   */
  static public function intersectArrays($p1, $p2) {
    $result = array();
    foreach($p1 as $tsa) {
      foreach($p2 as $tsb) {
        if ($tsa->overlaps($tsb)) {
          $result[] = $tsa->intersect($tsb);
        }
      }
    }
    usort($result, array(static::class, 'compare'));
    return $result;
  }

  /** Sort in place array of Timespans by beginning, ending
   * @param Timespan[] $p1
   */
  static public function sortArray(&$p1) {
    usort($p1, (array(static::class, 'compare')));
  }
}