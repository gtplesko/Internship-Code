<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/10/2017
 * Time: 11:24 AM
 */

namespace Drupal\reserved\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\e\Controller\AjaxPageControllerBase;
use Drupal\Core\Ajax\HtmlCommand;

abstract class CenterDashControllerBase extends AjaxPageControllerBase {

  const DEFAULT_ACTION = '';
  const LAYOUT = 'scheduling/dashboard';
  /**
   * @var \Drupal\reserved\ReservEdService;
   */
  protected $reservEd;
  protected $nav;
  protected $center;

  public static function create(ContainerInterface $container) {
    $controller = new static();
    $controller->reservEd = $container->get('tas.reserved');

    return $controller;
  }

  public function __construct() {
    parent::__construct();
    $this->libraries[] = 'reserved/scheduling';

  }

  public function dashboard($center_code, $action) {
    $centerManager = $this->reservEd->getCenterManagerService();
    $center = $centerManager->getCenter(strtoupper($center_code));
    if(!$center->center_code) {
      throw new NotFoundHttpException();
    } else {
      $this->center = $center;
      $this->nav = $this->makeNav();
      $this->context->center_code = $center->center_code;
      $this->context->title = $center->title;
      // dummy hack to get AjaxPageController not to break on blank.
      if (!$action) $action = 'home';
      return $this->page($action);
    }
  }
  public function render($section, $content){
    if ($this->jsMode != 'nojs' && $this->jsMode != 'drupal_modal') {
      $this->commands[] = new HtmlCommand('#'. $section, $content);
    }
    else {
      $this->build[$section] = $content;
    }
  }
  abstract protected function makeNav();

}