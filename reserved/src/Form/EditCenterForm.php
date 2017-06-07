<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/24/2017
 * Time: 3:07 PM
 */

namespace Drupal\reserved\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\e\Form\AjaxFormTrait;
use Drupal\reserved\Controller\Dashboard;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EditCenterForm extends FormBase {

  use AjaxFormTrait;

  /** @var  \Tas\ReservEd\Center\CenterManager */
  protected $centerManager;

  public static function create(ContainerInterface $container) {
    $form = new static();
    $form->reservEdService = $container->get('tas.reserved');
    $form->centerManager = $form->reservEdService->getCenterManagerService();
    return $form;
  }

  public function getFormId() {
    return 'reserved_edit_center_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['title'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 100,
      '#required'  => TRUE,
    ];

    $form['center_code'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Center Code:'),
      '#maxlength' => 30,
      '#required'  => TRUE,
    ];

    $form['inactive'] = [
      '#type'  => 'checkbox',
      '#title' => $this->t('Inactive'),
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
    $center = $this->centerManager->getCenter($values['center_code']);
    $center->center_code = $values['center_code'];
    $center->title = $values['title'];
    $center->status_int = $values['inactive'];

    $this->centerManager->saveCenter($center);

    $controller = Dashboard::service();
    $this->allow_redirect = !$controller->isAjaxCall();

    $controller->route('centers');
  }
}