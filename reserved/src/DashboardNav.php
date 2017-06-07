<?php
/**
 * Created by PhpStorm.
 * User: plegav14
 * Date: 5/9/2017
 * Time: 2:34 PM
 */

namespace Drupal\reserved;

use Drupal\Core\Link;

class DashboardNav {

  protected $sections;
  protected $hamburger_items;

  public function addSection($section){
    $this->sections[] = $section;
  }

  public function addToHamburger($item){
    $this->hamburger_items[] = $item;
  }

  public function render(){

    $list = [
      '#theme'=>'item_list',
      '#items'=>$this->sections,
      '#title'=>'',
      '#list_type'=>'ul',
      '#attributes'=>[
        'id'=>'scheduling_nav'
      ],
    ];
    $nav = [
      '#prefix'=>'<nav>',
      '#suffix'=>'</nav>',
    ];

    $hamburger = [
      '#prefix'=>'<div id="hamburger">',
      '#suffix'=>'</div>',
    ];
    $hamburger['hamburger_button'] = [
      '#markup'=>'<button class="button">&#x2630;</button>',
      '#allowed_tags' => ['button'],
    ];
    $hamburger_list = [
      '#theme'=>'item_list',
      '#items'=>$this->hamburger_items,
      '#title'=>'',
      '#list_type'=>'ul',
      '#attributes'=>[
        // 'id'=>'hamburger-menu',

      ],
      '#prefix'=>'<div id="hamburger-menu">',
      '#suffix'=>'</div>',

    ];
    $hamburger['hamburger_list']=$hamburger_list;
    $nav['hamburger']=$hamburger;
    $nav['list'] = $list;
    return \Drupal::service('renderer')->render($nav);
  }
}