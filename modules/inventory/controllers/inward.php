<?php
/**
 * @filesource modules/inventory/controllers/inward.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Inventory\Inward;

use \Kotchasan\Http\Request;
use \Gcms\Login;
use \Kotchasan\Html;
use \Kotchasan\Language;

/**
 * module=inventory-inward
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * รายงานการซื้อสินค้า
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // เลือกเมนู
    $this->menu = 'buy';
    // member, can_buy
    if ($login = Login::checkPermission(Login::isMember(), 'can_buy')) {
      // ค่าที่ส่งมา
      $owner = (object)array(
          'typies' => Language::get('BUY_TYPIES'),
          'year' => $request->request('year', date('Y'))->toInt(),
          'month' => $request->request('month', date('m'))->toInt(),
          'status' => $request->request('status')->toInt()
      );
      $owner->status = isset($owner->typies[$owner->status]) ? $owner->status : self::$cfg->inward_status;
      $title = '{LNG_Order Report} '.$owner->typies[$owner->status];
      if ($owner->month > 0) {
        $title .= ' {LNG_month}  '.Language::find('MONTH_LONG', null, $owner->month);
      }
      if ($owner->year > 0) {
        $title .= ' {LNG_year} '.($owner->year + Language::get('YEAR_OFFSET'));
      }
      $this->title = Language::trans($title);
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><a href="index.php" class="icon-home">{LNG_Home}</a></li>');
      $ul->appendChild('<li><span>{LNG_Buy}</span></li>');
      $ul->appendChild('<li><span>{LNG_Report}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-report">'.$this->title.'</h2>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('Inventory\Inward\View')->render($request, $owner));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}
