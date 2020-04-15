<?php

/**
 * ToyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author ToyyibPay Team
 */
 
// Versioning
$_['toyyibpay_ptype'] = "OpenCart";
$_['toyyibpay_pversion'] = "2.3";

// Heading
$_['heading_title'] = 'ToyyibPay Payment Gateway';

// Text
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified ToyyibPay Malaysia Online Payment Gateway account details!';
$_['text_edit'] = 'Edit ToyyibPay';
$_['text_toyyibpay'] = '<a onclick="window.open(\'https://toyyibpay.com/\');" style="text-decoration:none;"><img src="view/image/payment/logo.png" alt="ToyyibPay" title="ToyyibPay Payment Gateway" style="border: 0px solid #EEEEEE;" height=25 width=94/></a>';

// Entry
$_['toyyibpay_api_key'] = 'API Secret Key';
$_['toyyibpay_api_environment'] = 'API Environment';
$_['toyyibpay_category_code'] = 'ToyyibPay Category Code';
$_['toyyibpay_payment_channel'] = 'ToyyibPay Payment Channel';
$_['toyyibpay_payment_charge'] = 'ToyyibPay Payment Charge';
$_['toyyibpay_extra_email'] = 'Extra e-mail to customer';
$_['toyyibpay_bill_name'] = 'ToyyibPay Billing Name';
$_['toyyibpay_log_file'] = 'ToyyibPay Transaction Log';
$_['toyyibpay_log_api'] = 'ToyyibPay API Log';

$_['entry_minlimit'] = 'Minimum Limit';
$_['entry_completed_status'] = 'Completed Status';
$_['entry_geo_zone'] = 'Geo Zone';
$_['entry_status'] = 'Status';
$_['entry_sort_order'] = 'Sort Order';

// Help
$_['help_api_key'] = 'Please enter your ToyyibPay API Key. <a href=\'https://toyyibpay.com/index.php/dashboard\' target=\'_blank\'>Get Your API Key</a>';
$_['help_category_code'] = 'Please enter your Category Code. <a href=\'https://toyyibpay.com/index.php/category\' target=\'_blank\'>Check Your Category</a>';
$_['help_minlimit'] = 'Set total minimum limit to enable ToyyibPay conditionally';
$_['help_api_environment'] = 'Select ToyyibPay Payment Environment (Production or Sandbox)';
$_['help_bill_name'] = 'ToyyibPay BillName. E.g.: Opencart Billing';
$_['help_log_api'] = 'Create API Payment Log';
$_['help_log_file'] = 'Enable Log File. Please refer to configuration file for log file location.';
$_['help_payment_channel'] = 'Online Banking or Credit Card Channel';
$_['help_payment_charge'] = 'Impose the transaction charge on transaction amount or add extra to the customer.';
$_['help_extra_email'] = 'Edit here to send extra e-mail from toyyibPay or delete this to not send extra e-mail.';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify ToyyibPay Extensions!';
$_['error_api_key'] = '<b>ToyyibPay API Key</b> Required!';
$_['error_api_environment'] = '<b>ToyyibPay API Environment</b> Required!';
$_['error_payment_channel'] = '<b>ToyyibPay Payment Channel</b> Required!';
$_['error_payment_charge'] = '<b>ToyyibPay Payment Charge</b> Required!';
