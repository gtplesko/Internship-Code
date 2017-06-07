<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/24/2017
 * Time: 11:42 AM
 */

namespace Drupal\reserved\Form;

use Tas\ReservEd\Space\Space;
use Drupal\Core\Form\FormStateInterface;

trait SpaceFromFormStateTrait {
  protected function spaceFromFormState(FormStateInterface $form_state) {
    $storage = $form_state->getStorage();
    if (empty($storage['space'])) {
      if (empty($_GET['space_code'])) {
        $space = new Space;
      } else {
        // TODO prevent direct object reference vuln
        $space = $this->getSpaceManager()->getSpace($_GET['space_code'], TRUE);
      }
      $storage['space'] = $space;
      $form_state->setStorage($storage);
    }

    $possible_center_code = \Drupal::routeMatch()->getParameter('center_code');
    if (!empty($possible_center_code)) {
      $storage['space']->center_code = $possible_center_code;
    }
    return $storage['space'];
  }
  abstract protected function getSpaceManager();
}