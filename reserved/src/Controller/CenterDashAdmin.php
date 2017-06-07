<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/26/2017
 * Time: 3:35 PM
 */

namespace Drupal\reserved\Controller;


use Drupal\e\Controller\AjaxPageControllerBase;

use Drupal\reserved\DashboardNav;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\reserved\Form\EditProficiencyForm;
use Drupal\reserved\Form\Space\EditSpaceForm;
use Drupal\Core\Link;

class CenterDashAdmin extends CenterDashControllerBase {

  public function route($action) {
    switch ($action) {
      case 'spaces':
        $this->setUrl($action);
        $this->report('scheduling_menu', 'scheduling/spaces');
        $this->report('scheduling_details','scheduling/space_overview');
        break;
      case 'space-view':
        $this->setUrl($action);
        $this->report('scheduling_details', 'scheduling/view_space');
        break;
      case 'space-edit':
        $this->getModalForm('scheduling_details', EditSpaceForm::class,'Edit Space', ['width' => '60vw', 'height'=>'100%']);
        break;
      case 'space-add':
        $this->getModalForm('scheduling_details', EditSpaceForm::class,'Add Space', ['width' => '60vw', 'height'=>'100%']);
        break;
      case 'proficiencies':
        $this->setUrl($action);
        $this->report('scheduling_menu', 'scheduling/proficiencies');
        $this->report('scheduling_details','scheduling/proficiency_overview');
        break;
      case 'proficiency-view':
        $this->setUrl($action);
        $this->report('scheduling_details','scheduling/view_proficiency');
        break;
      case 'proficiency-edit':
        $this->getModalForm('scheduling_details',EditProficiencyForm::class,'Edit Proficiency',['width' => '60vw', 'height'=>'100%']);
        break;
      case 'proficiency-add':
        $this->getModalForm('scheduling_details',EditProficiencyForm::class,'Add Proficiency',['width' => '60vw', 'height'=>'100%']);
        break;
      case 'home':
        break;
    }
    $this->context->nav = $this->nav->render();
  }
  protected function makeNav(){
    $nav = new DashboardNav();
    $nav->addToHamburger(Link::createFromRoute('Select Center', 'reserved.scheduling-dashboard', ['action'=>'centers']));
    $nav->addToHamburger(Link::createFromRoute('Schedule Desk', 'reserved.scheduling-center-dashboard',
      ['center_code'=>strtolower($this->center->center_code), 'action'=>'space-groups']));
    $nav->addSection(Link::createFromRoute('Spaces', 'reserved.scheduling-center-dashboard-admin',
      ['center_code'=>strtolower($this->center->center_code), 'action'=>'spaces']));
    $nav->addSection(Link::createFromRoute('Proficiencies', 'reserved.scheduling-center-dashboard-admin',
      ['center_code'=>strtolower($this->center->center_code), 'action'=>'proficiencies']));
    return $nav;
  }

}