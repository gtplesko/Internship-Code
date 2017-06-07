<?php
/**
 * Created by PhpStorm.
 * User: metzlerd
 * Date: 3/29/2017
 * Time: 1:47 PM
 */

namespace Drupal\reserved\Controller;


use Drupal\Core\Link;
use Drupal\e\Controller\AjaxPageControllerBase;
use Drupal\reserved\Form\EditCenterForm;
use Drupal\reserved\Form\EditProficiencyForm;
use Drupal\reserved\Form\Space\EditSpaceForm;

// Main dashboard for space schedulers.
class Dashboard extends AjaxPageControllerBase {

  const DEFAULT_ACTION = 'intro';
  const LAYOUT = 'scheduling/dashboard';

  public function __construct() {
    parent::__construct();
    $this->libraries[] = 'reserved/scheduling';

  }

  /**
   * @inheritdoc
   */
  public function route($action) {
    switch ($action) {
      case 'intro':
        $this->route('centers');
        $this->report('scheduling_details', 'scheduling/intro');
        break;
      case 'center-add':
        $this->getModalForm('scheduling_details',EditCenterForm::class,'Add Center',['width' => '60vw', 'height'=>'100%']);
        break;
      case 'centers':
        $this->report('scheduling_menu', 'scheduling/centers');
        break;
      /*
        case 'spaces':
        $this->setUrl($action);
        $this->report('scheduling_menu', 'scheduling/spaces');
        break;
      case 'space-edit':
        $this->getModalForm('scheduling_details', EditSpaceForm::class,'Edit Space', ['width' => '60vw', 'height'=>'100%']);
        break;
      case 'space-add':
        $this->getModalForm('scheduling_details', EditSpaceForm::class,'Add Space', ['width' => '60vw', 'height'=>'100%']);
        break;
      case 'space-view':
        $this->setUrl($action);
        $this->report('scheduling_details', 'scheduling/view_space');
        break;
      case 'proficiency-add':
        $this->route('proficiencies');
        $this->getModalForm('scheduling_details',EditProficiencyForm::class,'Add Proficiency',['width' => '60vw', 'height'=>'100%']);
        break;
      case 'proficiency-view':
        $this->report('scheduling_details','scheduling/view_proficiency');
        break;
      case 'proficiency-edit':
        $this->getModalForm('scheduling_details',EditProficiencyForm::class,'Edit Proficiency',['width' => '60vw', 'height'=>'100%']);
        break;
      case 'proficiencies':
        $this->setUrl($action);
        $this->report('scheduling_menu', 'scheduling/proficiencies');
        break;
      */
      default:

    }
  }
}