<?php

/**
 * ToyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author ToyyibPay Team
 */
// Versioning
$_['toyyibpay_ptype'] = "OpenCart";
$_['toyyibpay_pversion'] = "1.5";

// Heading
$_['heading_title'] = 'ToyyibPay Malaysia Online Payment Gateway';

// Text 
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified ToyyibPay Malaysia Online Payment Gateway account details!';
$_['text_toyyibpay'] = '<a onclick="window.open(\'https://toyyibpay.com/\');" style="text-decoration:none;"><img src="view/image/payment/toyyibpay-logo.png" alt="ToyyibPay Online Payment Gateway" title="ToyyibPay Malaysia Online Payment Gateway" style="border: 0px solid #EEEEEE;" height=25 width=94/></a>';

// Entry
$_['entry_api_key'] = 'ToyyibPay API Key :<br /><span class="help">[Required] Please refer to your ToyyibPay account.</span>';
$_['entry_api_environment'] = 'ToyyibPay API Environment :<br /><span class="help">[Required] Production / Sandbox Mode.</span>';
$_['entry_category_code'] = 'ToyyibPay Category Code :<br /><span class="help">[Important] Please refer to your ToyyibPay account.</span>';

$_['entry_bill_name'] = 'ToyyibPay Billing Name :<br /><span class="help">[Optional] E.g.: Opencart Billing</span>';
$_['entry_log_file'] = 'ToyyibPay Transaction Log :<br /><span class="help">[Optional] Please refer to configuration file for log file location.</span>';
$_['entry_log_api'] = 'ToyyibPay API Log <span class="help"> </span> :';

$_['entry_payment_channel'] = 'ToyyibPay Payment Channel :<br /><span class="help">[Important] Online Banking / Credit Card Channel.</span>';
$_['entry_payment_charge'] = 'ToyyibPay Payment Charge :<br /><span class="help">[Important] Impose the transaction charge on transaction amount or add extra to the customer.</span>';
$_['entry_extra_email'] = 'Extra e-mail to customer :<br /><span class="help">[Optional] Edit here to send extra e-mail from toyyibPay or delete this to not send extra e-mail.</span>';

$_['entry_order_status'] = 'Order Status :';
$_['entry_pending_status'] = 'Pending Status :';
$_['entry_success_status'] = 'Success Status :';
$_['entry_failed_status'] = 'Failed Status :';
$_['entry_status'] = 'Status :';
$_['entry_sort_order'] = 'Sort Order :';
$_['entry_minlimit'] = 'Minimum Limit : RM<br /><span class="help">Please input 0 if you do not want to use this feature.</span>';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify ToyyibPay Malaysia Online Payment Gateway!';
$_['error_api_key'] = '<b>ToyyibPay API Key</b> Required!';
$_['error_api_environment'] = '<b>ToyyibPay API Environment</b> Required!';
$_['error_category_code'] = '<b>ToyyibPay Category Code</b> Required!';
$_['error_payment_channel'] = '<b>ToyyibPay Payment Channel</b> Required!';
$_['error_payment_charge'] = '<b>ToyyibPay Payment Charge</b> Required!';
$_['error_settings'] = 'ToyyibPay API Key and Category Code  mismatch, contact team@toyyibpay.com to assist.';
$_['error_status'] = 'Unable to connect ToyyibPay API.';