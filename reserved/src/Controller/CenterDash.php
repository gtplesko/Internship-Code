<?php
/**
 * Created by PhpStorm.
 * User: briluc21
 * Date: 4/26/2017
 * Time: 3:35 PM
 */

namespace Drupal\reserved\Controller;


use Drupal\e\Controller\AjaxPageControllerBase;

use Drupal\Core\Link;
use Drupal\reserved\DashboardNav;
use Drupal\reserved\Form\BookingSimpleForm;
use Drupal\reserved\Form\EditProficiencyForm;
use Drupal\reserved\Form\SelectDateRange;
use Drupal\reserved\Form\Space\EditSpaceForm;
use Tas\ReservEd\ScheduleEntry;
use Tas\ReservEd\SvgSchedule;
use Drupal\Core\Form\FormState;


class CenterDash extends CenterDashControllerBase {

  public function route($action) {
    switch ($action) {

      case 'space-groups':
        $this->setUrl($action);
        $this->report('scheduling_menu', 'scheduling/space_groups');
        break;

      case 'space-group-view':

        $form_state = new FormState;
        $form_state->setStorage(['grouping_code'=>$_GET['grouping_code']]);
        $this->route('space-groups');
        $selectDateForm = \Drupal::formBuilder()->buildForm(SelectDateRange::class, $form_state);
        $svgGraph = $this->makeSvgScheduleGraph($form_state->getStorage()['start_date'], $form_state->getStorage()['end_date']);
        $this->render('scheduling_header',$selectDateForm);
        $this->render('scheduling_details',$svgGraph);
        $this->setUrl($action);
        break;

      case 'space-view':
        $this->setUrl($action);
        $this->report('scheduling_details', 'scheduling/view_space');
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
      case 'booking-add':
        $this->getModalForm('scheduling_details',BookingSimpleForm::class,'Schedule',['width' => '60vw', 'height'=>'100%']);
        break;
      case 'home':
        break;
    }

    $this->context->nav = $this->nav->render();
  }
  protected function makeNav(){
    $nav = new DashboardNav();
    $nav->addToHamburger(Link::createFromRoute('Admin', 'reserved.scheduling-center-dashboard-admin',
      ['center_code'=>strtolower($this->center->center_code), 'action'=>'spaces']));
    $nav->addToHamburger(Link::createFromRoute('Select Center', 'reserved.scheduling-dashboard', ['action'=>'centers']));
    $nav->addSection(Link::createFromRoute('Spaces', 'reserved.scheduling-center-dashboard',
      ['center_code'=>strtolower($this->center->center_code), 'action'=>'space-groups']));
    $nav->addSection(Link::createFromRoute('Proficiencies', 'reserved.scheduling-center-dashboard',
      ['center_code'=>strtolower($this->center->center_code), 'action'=>'proficiencies']));
    return $nav;
  }
  private function makeSvgScheduleGraph($start_date, $end_date){
    $grouping_code = @$_GET['grouping_code'];
    $center_code = $this->center->center_code;
    //print_r([$start_date, $end_date]);
    $entries = $this->reservEd->getSchedulingManagerService()
      ->getScheduleForSpaceGroup($center_code, $grouping_code, $start_date, $end_date);
    //print_r($entries);
    $sections = [];
    $svgScheduleBuilder = new SvgSchedule();
    $svgScheduleBuilder->setDateRange($start_date, $end_date);

    foreach($entries as $space_code=>$bookingEntries){
      $link = (Link::createFromRoute(
        'Create Booking',
        'reserved.scheduling-center-dashboard',
        [ 'center_code'=>strtolower($this->center->center_code), 'action'=>'booking-add'],
        [ 'query'=>['space_code'=>$space_code],
          'attributes'=>['class'=>['use-ajax']
      ]]
      ));
      $svgGraph =  $svgScheduleBuilder->svgFromEntries($bookingEntries) ;

      $sections[] = [
        '#type' => 'inline_template',
        '#template' => '<div class="left-2-7">{{space_code}} {{link}}</div>'.
                       '<div class="right-4-7" style="flex-grow:2">{{svg|raw}}</div>',
        '#context' => [
          'space_code' => $space_code,
          'link' => $link,
          'svg' =>$svgGraph
        ]
      ];
    }
    $list = [
      '#theme'=>'item_list',
      '#items'=>$sections,
      '#title'=>'',
      '#list_type'=>'ul',
      '#attributes'=>[
        'class'=>[
          'space-groups-space-list'
        ]
      ],
    ];
    return $list;
  }


}