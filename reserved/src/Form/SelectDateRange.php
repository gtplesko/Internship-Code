<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/31/2017
 * Time: 2:31 PM
 */

namespace Drupal\reserved\Form;


use Drupal\e\Form\AjaxFormTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\reserved\Controller\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tas\ReservEd\Scheduling\Activity;
use Tas\ReservEd\Scheduling\Booking;
use Tas\ReservEd\Scheduling\Event;
use Tas\ReservEd\Space\Space;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Datetime\DrupalDateTime;

class SelectDateRange extends FormBase {

  use AjaxFormTrait;
  public static $start_date;
  public static $end_date;

  public static function create(ContainerInterface $container) {
    $form = new static();
    return $form;
  }

  public function getFormId() {
    return 'reserved_select_date_range';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form_state->setMethod('GET');
    $defaultStartDate = $this->normalizeDateOnWeek((new \DateTime('now')));
    $defaultEndDate = clone $defaultStartDate;
    $defaultEndDate->add(new \DateInterval('P1W'));

    $values = [];
    if(isset($_GET['start_time'])){
      $values['start_time'] = new \DateTime($_GET['start_time']['date']. ' ' . $_GET['start_time']['time']);
    }else{
      $values['start_time'] = $defaultStartDate;
    }
    if(isset($_GET['end_time'])){
      $values['end_time'] = new \DateTime($_GET['end_time']['date']. ' ' . $_GET['end_time']['time']);
    }else{
      $values['end_time'] = $defaultEndDate;
    }
    $storage = $form_state->getStorage();

    $grouping_code = $storage['grouping_code'];
    $storage['start_date'] = $values['start_time'];
    $storage['end_date'] = $values['end_time'];

    $form_state->setStorage($storage);
    $form['#method'] = 'get';
    $form['times'] = [
      '#type' => 'fieldset'
    ];


    $form['times']['start_time'] = [
      '#type'  => 'datetime',
      '#title' => $this->t('Start Time'),
      '#required'  => TRUE,
      '#default_value' => DrupalDateTime::createFromDateTime($values['start_time']),
    ];


    $form['times']['end_time'] = [
      '#type'  => 'datetime',
      '#title' => $this->t('End Time'),
      '#required'  => TRUE,
      '#default_value' => DrupalDateTime::createFromDateTime($values['end_time']),
    ];
    $form['times']['grouping_code'] = [
      '#type' => 'hidden',
      '#value' => $grouping_code,
    ];


    $form['times']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    //$this->bindAjaxForm(Dashboard::service(), $form, $form_state);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

  }


  private function normalizeDateOnWeek($date) {
    $time = $date->add((new \DateInterval('P1D')))->getTimestamp();
    $date->setTimestamp(strtotime('last Sunday', $time));
    return $date;
  }
}