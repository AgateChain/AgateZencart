<?php

class agate extends base
{
  public $code;
  public $title;
  public $description;
  public $sort_order;
  public $enabled;

  private $api_key;
  private $test_mode;

  function agate()
  {
    $this->code             = 'agate';
    $this->title            = MODULE_PAYMENT_AGATE_TEXT_TITLE;
    $this->description      = MODULE_PAYMENT_AGATE_TEXT_DESCRIPTION;
    $this->api_key          = MODULE_PAYMENT_AGATE_API_KEY;
    $this->sort_order       = MODULE_PAYMENT_AGATE_SORT_ORDER;
    $this->enabled          = ((MODULE_PAYMENT_AGATE_STATUS == 'True') ? true : false);

  }

  function javascript_validation()
  {
    return false;
  }

  function log($contents){
    error_log($contents);
  }

  function selection()
  {
    return array('id' => $this->code, 'module' => $this->title);
  }

  function pre_confirmation_check()
  {
    return false;
  }

  function confirmation()
  {
    return false;
  }

  function process_button()
  {
    return false;
  }

  function before_process()
  {
    return false;
  }

  function after_process()
  {
    global $insert_id, $db, $order;

    $info = $order->info;

    $configuration = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key='STORE_NAME' limit 1");

    require_once(dirname(__FILE__) . "/Agate/init.php");
    require_once(dirname(__FILE__) . "/Agate/version.php");

    $redirect_url   = zen_href_link('account');
    $order_total    = $order->info['total'];
    $baseUri        = "http://gateway.agate.services/" ;
    $convertUrl     = "http://gateway.agate.services/convert/";
    $api_key        = $this->api_key;
    $currencySymbol = $order->info['currency'];

    $amount_iUSD = \Agate\Merchant\Order::convertCurToIUSD($convertUrl, $order_total, $api_key, $currencySymbol);

    echo \Agate\Merchant\Order::redirectPayment($baseUri, $amount_iUSD, $order_total, $currencySymbol, $api_key, $redirect_url);

    return false;

  }

  function check()
  {
      global $db;

      if (!isset($this->_check)) {
          $check_query  = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_AGATE_STATUS'");
          $this->_check = $check_query->RecordCount();
      }

      return $this->_check;
  }

  function install()
  {
    global $db, $messageStack;

    if (defined('MODULE_PAYMENT_AGATE_STATUS')) {
      $messageStack->add_session('Agate module already installed.', 'error');
      zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=agate', 'NONSSL'));

      return 'failed';
    }

    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values('Enable Agate Module', 'MODULE_PAYMENT_AGATE_STATUS', 'False', 'Enable the Agate plugin?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values('Agate API KEY', 'MODULE_PAYMENT_AGATE_API_KEY', '0', 'Your Agate API KEY', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values('Sort order of display.', 'MODULE_PAYMENT_AGATE_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '8', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values('Set Pending Order Status', 'MODULE_PAYMENT_AGATE_PENDING_STATUS_ID', '" . intval(DEFAULT_ORDERS_STATUS_ID) .  "', 'Status in your store when Agate Invoice status is pending.<br />(\'Pending\' recommended)', '6', '5', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values('Set Awaiting Order confimation Status', 'MODULE_PAYMENT_AGATE_PAID_STATUS_ID', '2', 'Status in your store when Agate Invoice status is paid and awaiting confirmation from bitcoin network.<br />(\'Awaiting Confirmation\' recommended)', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values('Set Paid Order Status', 'MODULE_PAYMENT_AGATE_CONFIRMED_STATUS_ID', '2', 'Status in your store when Agate confirms the Invoice payment.<br />(\'Confirmed\' recommended)', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values('Set Expired Order Status', 'MODULE_PAYMENT_AGATE_EXPIRED_STATUS_ID', '7', 'Status in your store when Agate Invoice status is expired.<br />(\'Expired\' recommended)', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values('Set Canceled Order Status', 'MODULE_PAYMENT_AGATE_CANCELED_STATUS_ID', '7', 'Status in your store when Agate Invoice status is canceled.<br />(\'Canceled\' recommended)', '6', '6', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
  }

  function remove()
  {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE\_PAYMENT\_AGATE\_%'");
  }

  function keys()
  {
    return array(
      'MODULE_PAYMENT_AGATE_STATUS',
      'MODULE_PAYMENT_AGATE_API_KEY',
      'MODULE_PAYMENT_AGATE_SORT_ORDER',
      'MODULE_PAYMENT_AGATE_PENDING_STATUS_ID',
      'MODULE_PAYMENT_AGATE_CONFIRMED_STATUS_ID',
      'MODULE_PAYMENT_AGATE_PAID_STATUS_ID',
      'MODULE_PAYMENT_AGATE_EXPIRED_STATUS_ID',
      'MODULE_PAYMENT_AGATE_CANCELED_STATUS_ID'
    );
  }
}

function agate_censorize($value) {
  return "(hidden for security reasons)";
}
