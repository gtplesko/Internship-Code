<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/13/2017
 * Time: 11:37 AM
 */

namespace Drupal\reserved\Form\Space;


use Drupal\Core\Form\FormStateInterface;
use Drupal\e\Form\AjaxFormTrait;
use Drupal\reserved\Controller\Dashboard;
use Drupal\reserved\Form\SpaceFromFormStateTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tas\ReservEd\Space\Space;

class EditSpaceForm extends \Drupal\Core\Form\FormBase {

  use AjaxFormTrait;
  use SpaceFromFormStateTrait;

  /** @var  \Drupal\reserved\ReservEdService */
  protected $reservEdService;

  /** @var  \Tas\ReservEd\Space\SpaceManager */
  protected $spaceManager;
  /**
   * @var \Tas\ReservEd\Center\CenterManager
   */
  protected $centerManager;



  public static function create(ContainerInterface $container) {
    $form = new static();
    $form->reservEdService = $container->get('tas.reserved');
    $form->spaceManager = $form->reservEdService->getSpaceManagerService();
    $form->centerManager = $form->reservEdService->getCenterManagerService();
    return $form;
  }
  public function getFormId() { return "reserved_edit_space_form"; }

  public function buildForm(array $form, FormStateInterface $form_state) {

    /** @var Space $space */
    $space = $this->spaceFromFormState($form_state);

    $form['title'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 100,
      '#required'  => TRUE,
      '#default_value' => $space->title,
    ];

    $form['space_code'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Space Code:'),
      '#maxlength' => 30,
      '#required'  => TRUE,
      '#default_value' => $space->space_code,
      '#disabled' => (is_null($space->space_code))? FALSE : TRUE,
    ];

    $form['location'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Location Information:'),
    ];

    $campus_options = $this->objectsToOptionsList(
      $this->spaceManager->getAllCampuses(),
      'campus_code',
      'description'
    );
    $form['location']['campus_code'] =  [
      '#type'  => 'select',
      '#title' => $this->t('Campus:'),
      '#options' => $campus_options,
      '#required'  => TRUE,
      '#default_value' => $space->campus_code,
    ];

    $form['location']['building_code'] =  [
      '#type'  => 'textfield',
      '#title' => $this->t('Building Code:'),
      '#maxlength' => 30,
      '#default_value' => $space->building_code,
    ];

    $form['location']['floor'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Floor:'),
      '#maxlength' => 1,
      '#default_value' => $space->floor,
    ];

    $form['location']['room'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Room:'),
      '#maxlength' => 10,
    ];
    $form['space_type'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Space Type:'),
      '#maxlength' => 30,
      '#default_value' => $space->space_type,
    ];

    $form['capacity'] = [
      '#type'   => 'number',
      '#title'  => $this->t('Capacity'),
      '#default_value' => $space->capacity,
    ];

    $form['inactive'] = [
      '#type'  => 'checkbox',
      '#title' => $this->t('Inactive'),
      '#default_value' => $space->inactive_ind,
    ];
    $center_options = $this->objectsToOptionsList(
      $this->centerManager->getAllCenters(),
      'center_code',
      'title'
    );
    $form['center_code'] = [
      '#type'  => 'select',
      '#title' => $this->t('Center:'),
      '#options' => $center_options,
      '#required'  => TRUE,
      '#default_value' => $space->center_code,
    ];
    $form['grouping_code'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('Group Code:'),
      '#maxlength' => 30,
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
    $original_space = $this->spaceFromFormState($form_state);

    /*
    // EDGE CASE - someone changes the space_code.
    if($original_space->space_code != $values['space_code']) {
      //TODO figure out exactly what is supposed to happen.
      // Possibly delete the old one and insert a new one?
      // Possibly throw error? not sure.
    }
     */

    //$edited_space = $this->spaceManager->getSpace($values['space_code']);


    // EDGE CASE - someone changes the space_code.
    // in this case the form adds a new space instead of
    // editing it.
    // im not sure what to do in that case.
    $original_space->space_code = $values['space_code'];
    $original_space->title = $values['title'];
    $original_space->campus_code = $values['campus_code'];
    $original_space->building_code = $values['building_code'];
    $original_space->floor = $values['floor'];
    $original_space->space_type = $values['space_type'];
    $original_space->capacity = $values['capacity'];
    $original_space->center_code = $values['grouping_code'];
    $original_space->center_code = $values['center_code'];
    $original_space->inactive_ind = $values['inactive'];

    $this->spaceManager->saveSpace($original_space);

    // Use factory methods to retrieve a service so that we can interact with it.
    $controller = Dashboard::service();
    $this->allow_redirect = !$controller->isAjaxCall();

    // re-render spaces to show the update.
    $controller->route('spaces');
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

  protected function getSpaceManager() {
    return $this->spaceManager;
  }

}