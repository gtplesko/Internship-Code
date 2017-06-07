<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/17/2017
 * Time: 10:05 AM
 */

namespace Drupal\reserved;

use Tas\Core\Application\Drupal8Application;
use Tas\Core\Model\DoctrineApp;
use Tas\ReservEd\Center\CenterManager;
use Tas\ReservEd\Proficiency\ProficiencyManager;
use Tas\ReservEd\Scheduling\SchedulingManager;
use Tas\ReservEd\Space\SpaceManager;

class ReservEdService {


  /** @var \Tas\Core\Model\DoctrineApp */
  protected $db;
  /** @var \Tas\Core\Application\Drupal8Application */
  protected $app;
  /** @var  \Tas\ReservEd\Space\SpaceManager */
  protected $spaceManager;
  /** @var  \Tas\ReservEd\Center\CenterManager */
  protected $centerManager;
  /** @var  \Tas\ReservEd\Proficiency\ProficiencyManager */
  protected $proficiencyManager;
  /** @var   \Tas\ReservEd\Scheduling\SchedulingManager */
  protected $schedulingManager;

  protected static $instance;

  public function __construct() {
    $this->app = Drupal8Application::service();
    $this->db = DoctrineApp::create($this->app);

  }

  /**
   * Singleton Factory.
   * @return ReservEdService
   */
  public static function service() {
    if (static::$instance === NULL) {
      static::$instance = new static();
    }
    return static::$instance;
  }

  public function getSpaceManagerService() {
    if(isset($this->spaceManager) == NULL) {
      $this->spaceManager = new SpaceManager($this->db);
    }
    return $this->spaceManager;
    //return $this->singleton(SpaceManager::class,'spaceManager');
  }
  public function getCenterManagerService() {
    if(isset($this->centerManager) == NULL) {
      $this->centerManager = new CenterManager($this->db);
    }
    return $this->centerManager;
    //return $this->singleton(SpaceManager::class,'centerManager');
  }
  public function getProficiencyManagerService() {
    if(isset($this->proficiencyManager) == NULL) {
      $this->proficiencyManager = new ProficiencyManager($this->db);
    }
    return $this->proficiencyManager;
    //return $this->singleton(ProficiencyManager::class,'proficiencyManager');
  }
  public function getSchedulingManagerService() {
    if(isset($this->schedulingManager) == NULL) {
      $this->schedulingManager = new SchedulingManager($this->db);
    }
    return $this->schedulingManager;
    //return $this->singleton(ProficiencyManager::class,'proficiencyManager');
  }

  /*
  // Ideas for better code
   public function get($id) {
    if (isset($this->managers[$id]) && isset($this->manager_class_names[$id]) {
      $manager_class = $this->manager_class_names[$id];
      $this->managers[$id] = new $manager_class($this->db);
    }
  }

  protected function singleton($class, $prop) {
    if(isset($this->$prop) == NULL) {
      $this->$prop= new $class($this->db);
    }
    return $this->$prop;
  }
   */
}