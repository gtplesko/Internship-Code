<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/23/2017
 * Time: 2:34 PM
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

class BookingSimpleForm extends FormBase {

  use AjaxFormTrait;
  use SpaceFromFormStateTrait;

  /** @var  \Tas\ReservEd\Scheduling\SchedulingManager */
  protected $schedulingManager;
  /** @var \Tas\ReservEd\Space\SpaceManager */
  protected $spaceManager;
  /** @var \Drupal\reserved\ReservEdService */
  protected $reservEdService;


  public static function create(ContainerInterface $container) {
    $form = new static();
    $form->reservEdService = $container->get('tas.reserved');
    $form->schedulingManager = $form->reservEdService->getSchedulingManagerService();
    $form->spaceManager = $form->reservEdService->getSpaceManagerService();
    return $form;
  }

  public function getFormId() {
    return 'reserved_edit_booking_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    /** @var Space $space */
    $space = $this->spaceFromFormState($form_state);

    $form['event_title'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Event Title'),
      '#maxlength' => 100,
      '#required'  => TRUE,
    ];

    $form['event_type'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Event Type'),
      '#maxlength' => 30,
      '#required'  => TRUE,
    ];

    $form['activity_title'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Activity Title'),
      '#maxlength' => 200,
      '#required'  => TRUE,
    ];
    $form['times'] = [
      '#type' => 'fieldset'
    ];


    $form['times']['start_time'] = [
      '#type'  => 'datetime',
      '#title' => $this->t('Start Time'),
      '#required'  => TRUE,
    ];


    $form['times']['end_time'] = [
      '#type'  => 'datetime',
      '#title' => $this->t('End Time'),
      '#required'  => TRUE,
    ];

    $form['space'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Space'),
      '#maxlength' => 30,
      '#required'  => TRUE,
      '#default_value' => $space->space_code,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    $this->bindAjaxForm(Dashboard::service(), $form, $form_state);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $start_date = $values['start_time']->format('Y-m-d H:i');// This formatter for some reason adds 5 minutes to input time
    $end_date = $values['end_time']->format('Y-m-d H:i');
    $event = new Event($values['event_title'],$values['event_type'],$start_date, $end_date);
    $event_id = $this->schedulingManager->saveEvent($event);
    $activity = new Activity($event_id, $values['activity_title'], $start_date, $end_date);
    $activity_id = $this->schedulingManager->saveActivity($activity);
    $booking = new Booking($activity_id, $values['space'], $start_date, $end_date);
    $this->schedulingManager->saveBooking($booking);

  }

  protected function getSpaceManager() {
    return $this->spaceManager;
  }
}