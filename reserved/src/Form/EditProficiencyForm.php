<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 4/25/2017
 * Time: 2:22 PM
 */

namespace Drupal\reserved\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\e\Form\AjaxFormTrait;
use Drupal\reserved\Controller\Dashboard;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tas\ReservEd\Proficiency\Proficiency;

class EditProficiencyForm extends FormBase {

  use AjaxFormTrait;

  /**
   * @var \Tas\ReservEd\Proficiency\ProficiencyManager
   */
  protected $proficiencyManager;

  public static function create(ContainerInterface $container) {
    $form = new static();
    $form->reservEdService = $container->get('tas.reserved');
    $form->spaceManager = $form->reservEdService->getSpaceManagerService();
    $form->centerManager = $form->reservEdService->getCenterManagerService();
    $form->proficiencyManager = $form->reservEdService->getProficiencyManagerService();
    return $form;
  }

  public function getFormId() { return "reserved_add_proficiency_form"; }

  public function buildForm(array $form, FormStateInterface $formState, Proficiency $proficiency=NULL) {
    $proficiency = $this->proficiencyFromFormState($formState);

    $form['title'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 100,
      '#required'  => TRUE,
      '#default_value' => $proficiency->title,
    ];

    $form['prof_code'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Proficiency Code:'),
      '#maxlength' => 30,
      '#required'  => TRUE,
      '#default_value' => $proficiency->prof_code,
      '#disabled' => (is_null($proficiency->prof_code))? FALSE : TRUE,
    ];

    $center_options = $this->objectsToOptionsList(
      $this->proficiencyManager->getAllCenters(),
      'center_code',
      'title'
    );
    $form['center_code'] = [
      '#type'  => 'select',
      '#title' => $this->t('Center:'),
      '#options' => $center_options,
      '#required'  => TRUE,
      '#default_value' => $proficiency->center_code,
    ];

    $form['inactive'] = [
      '#type'  => 'checkbox',
      '#title' => $this->t('Inactive'),
      '#default_value' => $proficiency->inactive_ind,
    ];

    $form['sort_order'] = [
      '#type'   => 'number',
      '#title'  => $this->t('Sort Order'),
      '#default_value' => $proficiency->sort_order,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    $this->bindAjaxForm(Dashboard::service(), $form, $formState);

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $proficiency = $this->proficiencyManager->getProficiency($values['prof_code']);
    $proficiency->prof_code=$values['prof_code'];
    $proficiency->title=$values['title'];
    $proficiency->center_code=$values['center_code'];
    $proficiency->inactive_ind=$values['inactive_ind'];
    $proficiency->sort_order=$values['sort_order'];

    $this->proficiencyManager->saveProficiency($proficiency);

    $controller = Dashboard::service();
    $this->allow_redirect = !$controller->isAjaxCall();

    $controller->route('proficiencies');
  }
  protected function proficiencyFromFormState(FormStateInterface $form_state) {
    $storage = $form_state->getStorage();
    if (empty($storage['proficiency'])) {
      if (empty($_GET['prof_code'])) {
        $proficiency = new Proficiency();
      } else {
        // TODO prevent direct object reference vuln
        $proficiency = $this->proficiencyManager->getProficiency($_GET['prof_code'], TRUE);
      }
      $storage['proficiency'] = $proficiency;
      $form_state->setStorage($storage);
    }

    $possible_center_code = \Drupal::routeMatch()->getParameter('center_code');
    if (!empty($possible_center_code)) {
      $storage['proficiency']->center_code = $possible_center_code;
    }

    return $storage['proficiency'];
  }
  /**
   * Converts an array of objects to something usable by the drupal #select form element
   * using $key_prop as the object property for the key and $val_prop for the display.
   *
   * @param array $objects
   * @param string $key_prop The property of the object to use as the key
   * @param string $val_prop The property of the object to use as the display value
   * @return array
   */
  protected function objectsToOptionsList($objects, $key_prop, $val_prop) {
    $options = [];
    foreach ($objects as $object) {
      $options[$object->$key_prop] = $object->$val_prop;
    }
    return $options;
  }
}