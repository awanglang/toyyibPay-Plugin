<?php

/**
 * ToyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author ToyyibPay Team
 */

class ControllerExtensionPaymentToyyibpay extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/payment/toyyibpay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_toyyibpay', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

		// Added in ToyyibPay for OpenCart 3.x
		$data['user_token'] = $this->session->data['user_token'];
        
		$data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['payment_toyyibpay_api_key'] = $this->language->get('toyyibpay_api_key');
		$data['toyyibpay_api_environment'] = $this->language->get('toyyibpay_api_environment');
        $data['payment_toyyibpay_category_code'] = $this->language->get('toyyibpay_category_code');
		
		$data['toyyibpay_payment_channel'] = $this->language->get('toyyibpay_payment_channel');
		$data['toyyibpay_payment_charge'] = $this->language->get('toyyibpay_payment_charge');
		$data['toyyibpay_extra_email'] = $this->language->get('toyyibpay_extra_email');
		$data['toyyibpay_bill_name'] = $this->language->get('toyyibpay_bill_name');
		$data['toyyibpay_log_file'] = $this->language->get('toyyibpay_log_file');
		$data['toyyibpay_log_api'] = $this->language->get('toyyibpay_log_api');
		
		$data['entry_minlimit'] = $this->language->get('entry_minlimit');
        $data['entry_completed_status'] = $this->language->get('entry_completed_status');
        $data['entry_pending_status'] = $this->language->get('entry_pending_status');
        $data['entry_failed_status'] = $this->language->get('entry_failed_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['help_api_key'] = $this->language->get('help_api_key');
		$data['help_api_environment'] = $this->language->get('help_api_environment');
		$data['help_payment_channel'] = $this->language->get('help_payment_channel');
		$data['help_payment_charge'] = $this->language->get('help_payment_charge');
		$data['help_category_code'] = $this->language->get('help_category_code');
        $data['help_bill_name'] = $this->language->get('help_bill_name');
        $data['help_log_file'] = $this->language->get('help_log_file');
        $data['help_log_api'] = $this->language->get('help_log_api');
        $data['help_extra_email'] = $this->language->get('help_extra_email');
        $data['help_minlimit'] = $this->language->get('help_minlimit');
		
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['api_key'])) {
            $data['error_api_key'] = $this->error['api_key'];
        } else {
            $data['error_api_key'] = '';
        }
		
        if (isset($this->error['api_environment'])) {
            $data['error_api_environment'] = $this->error['api_environment'];
        } else {
            $data['error_api_environment'] = '';
        }

        if (isset($this->error['payment_channel'])) {
            $data['error_payment_channel'] = $this->error['payment_channel'];
        } else {
            $data['error_payment_channel'] = '';
        }

        if (isset($this->error['payment_charge'])) {
            $data['error_payment_charge'] = $this->error['payment_charge'];
        } else {
            $data['error_payment_charge'] = '';
        }
		
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/toyyibpay', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/toyyibpay', 'user_token=' . $this->session->data['user_token'], true);
		
		//Added in OpenCart 3.x
		$data['idno'] = array("0" => "No Notification", "1" => "Email Notification", "2" => "SMS Notification", "3" => "Email & SMS Notification");
		$data['arrHost'] = array("1" => "Production Mode", "2" => "Sandbox Mode");
		$data['arrChannel'] = array("0" => "FPX", "1" => "Credit Card", "2" => "FPX & Credit Card");
		$data['arrCharge'] = array(
								"" => "All charge on transaction amount", 
								"0" => "Only FPX charge on customer", 
								"1" => "Only Crefit card charge on customer", 
								"2" => "All charge on customer"
							);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_toyyibpay_api_key_value'])) {
            $data['payment_toyyibpay_api_key_value'] = $this->request->post['payment_toyyibpay_api_key_value'];
        } else {
            $data['payment_toyyibpay_api_key_value'] = $this->config->get('payment_toyyibpay_api_key_value');
        }
        
        if (isset($this->request->post['payment_toyyibpay_category_code_value'])) {
            $data['payment_toyyibpay_category_code_value'] = $this->request->post['payment_toyyibpay_category_code_value'];
        } else {
            $data['payment_toyyibpay_category_code_value'] = $this->config->get('payment_toyyibpay_category_code_value');
        }
		
		if (isset($this->request->post['toyyibpay_api_environment_value'])) {
            $data['toyyibpay_api_environment_value'] = $this->request->post['toyyibpay_api_environment_value'];
        } else {
            $data['toyyibpay_api_environment_value'] = $this->config->get('toyyibpay_api_environment_value');
        }

        if (isset($this->request->post['toyyibpay_payment_channel_value'])) {
            $data['toyyibpay_payment_channel_value'] = $this->request->post['toyyibpay_payment_channel_value'];
        } else {
            $data['toyyibpay_payment_channel_value'] = $this->config->get('toyyibpay_payment_channel_value');
        }

        if (isset($this->request->post['toyyibpay_payment_charge_value'])) {
            $data['toyyibpay_payment_charge_value'] = $this->request->post['toyyibpay_payment_charge_value'];
        } else {
            $data['toyyibpay_payment_charge_value'] = $this->config->get('toyyibpay_payment_charge_value');
        }

        if (isset($this->request->post['toyyibpay_extra_email_value'])) {
            $data['toyyibpay_extra_email_value'] = $this->request->post['toyyibpay_extra_email_value'];
        } else {
            $data['toyyibpay_extra_email_value'] = $this->config->get('toyyibpay_extra_email_value');
        }

        if (isset($this->request->post['toyyibpay_bill_name_value'])) {
            $data['toyyibpay_bill_name_value'] = $this->request->post['toyyibpay_bill_name_value'];
        } else {
            $data['toyyibpay_bill_name_value'] = $this->config->get('toyyibpay_bill_name_value');
        }

        if (isset($this->request->post['toyyibpay_log_file_value'])) {
            $data['toyyibpay_log_file_value'] = $this->request->post['toyyibpay_log_file_value'];
        } else {
            $data['toyyibpay_log_file_value'] = $this->config->get('toyyibpay_log_file_value');
        }

        if (isset($this->request->post['toyyibpay_log_api_value'])) {
            $data['toyyibpay_log_api_value'] = $this->request->post['toyyibpay_log_api_value'];
        } else {
            $data['toyyibpay_log_api_value'] = $this->config->get('toyyibpay_log_api_value');
        }

        if (isset($this->request->post['payment_toyyibpay_minlimit'])) {
            $data['payment_toyyibpay_minlimit'] = $this->request->post['payment_toyyibpay_minlimit'];
        } else {
            $data['payment_toyyibpay_minlimit'] = $this->config->get('payment_toyyibpay_minlimit');
        }
        
        if (isset($this->request->post['payment_toyyibpay_completed_status_id'])) {
            $data['payment_toyyibpay_completed_status_id'] = $this->request->post['payment_toyyibpay_completed_status_id'];
        } else {
            $data['payment_toyyibpay_completed_status_id'] = $this->config->get('payment_toyyibpay_completed_status_id');
        }

        if (isset($this->request->post['payment_toyyibpay_pending_status_id'])) {
            $data['payment_toyyibpay_pending_status_id'] = $this->request->post['payment_toyyibpay_pending_status_id'];
        } else {
            $data['payment_toyyibpay_pending_status_id'] = $this->config->get('payment_toyyibpay_pending_status_id');
        }

        if (isset($this->request->post['payment_toyyibpay_failed_status_id'])) {
            $data['payment_toyyibpay_failed_status_id'] = $this->request->post['payment_toyyibpay_failed_status_id'];
        } else {
            $data['payment_toyyibpay_failed_status_id'] = $this->config->get('payment_toyyibpay_failed_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_toyyibpay_geo_zone_id'])) {
            $data['payment_toyyibpay_geo_zone_id'] = $this->request->post['payment_toyyibpay_geo_zone_id'];
        } else {
            $data['payment_toyyibpay_geo_zone_id'] = $this->config->get('payment_toyyibpay_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_toyyibpay_status'])) {
            $data['payment_toyyibpay_status'] = $this->request->post['payment_toyyibpay_status'];
        } else {
            $data['payment_toyyibpay_status'] = $this->config->get('payment_toyyibpay_status');
        }

        if (isset($this->request->post['payment_toyyibpay_sort_order'])) {
            $data['payment_toyyibpay_sort_order'] = $this->request->post['payment_toyyibpay_sort_order'];
        } else {
            $data['payment_toyyibpay_sort_order'] = $this->config->get('payment_toyyibpay_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/toyyibpay', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/toyyibpay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_toyyibpay_api_key_value']) {
            $this->error['api_key'] = $this->language->get('error_api_key');
        }
		
		// if (!$this->request->post['toyyibpay_category_code_value']) {
            // $this->error['category_code'] = $this->language->get('error_category_code');
        // }
		
		if (!$this->request->post['toyyibpay_api_environment_value']) {
            $this->error['api_environment'] = $this->language->get('error_api_environment');
        }
		
        if (!$this->request->post['toyyibpay_payment_channel_value']) {
            $this->error['payment_channel'] = $this->language->get('error_payment_channel');
        }

        if (!$this->request->post['toyyibpay_payment_charge_value']) {
            $this->error['payment_charge'] = $this->language->get('error_payment_charge');
        }

        return !$this->error;
    }

}
