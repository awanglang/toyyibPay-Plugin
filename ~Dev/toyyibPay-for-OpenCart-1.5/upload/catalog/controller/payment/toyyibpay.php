<?php
/**
 * ToyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author ToyyibPay Team
 * @LastUpdated on 202004142330
 */
 
require_once __DIR__ . '/toyyibpay-api.php';

class ControllerPaymentToyyibPay extends Controller
{
    private function get_domain_forwebmaster()
    {
        return substr($_SERVER['HTTP_HOST'], 0, 3) == 'www' ? substr($_SERVER['HTTP_HOST'], 4) : $_SERVER['HTTP_HOST'];
    }

    protected function index()
    {
        $this->language->load('payment/toyyibpay');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $localData['prod_name'][] = $product['name'];
            $localData['prod_desc'][] = $product['name'] . " x " . $product['quantity'];
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['description'] = '';
        $this->data['amount'] = '';
        $this->data['reference_1'] = '';
        $this->data['reference_1_label'] = '';
        $this->data['mobile'] = '';
        $this->data['name'] = '';
        $this->data['email'] = '';
        $this->data['redirect_url'] = '';
        $this->data['callback_url'] = '';
        $this->data['action'] = $this->url->link('payment/toyyibpay/proceed', '', true);
				
        $_SESSION['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $_SESSION['email'] = empty($order_info['email']) ? '' : $order_info['email'];
        $_SESSION['bill_name'] = implode($localData['prod_name']);
        $_SESSION['description'] = "Order " . $this->session->data['order_id'] . " - " . implode($localData['prod_desc']);
        $_SESSION['mobile'] = empty($order_info['telephone']) ? '' : $order_info['telephone'];
        $_SESSION['order_id'] = $this->session->data['order_id'];
        $_SESSION['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $_SESSION['redirect_url'] = $this->url->link('payment/toyyibpay/return_ipn', '', 'SSL');
        $_SESSION['callback_url'] = $this->url->link('payment/toyyibpay/callback_ipn', '', 'SSL');

        //$this->data['country'] = $order_info['payment_iso_code_2'];
        //$this->data['currency'] = $order_info['currency_code'];
        //$this->data['lang'] = $this->session->data['language'];
        $this->data['sha256'] = '';


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/toyyibpay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/toyyibpay.tpl';
        } else {
            $this->template = 'default/template/payment/toyyibpay.tpl';
        }

        $this->render();
		
    }
	
    public function proceed()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $api_key = $this->config->get('toyyibpay_api_key');
        $api_host = $this->config->get('toyyibpay_api_environment');
        $bill_name = $this->config->get('toyyibpay_bill_name');
		$category_code = $this->config->get('toyyibpay_category_code');
		$payment_channel = $this->config->get('toyyibpay_payment_channel');
        $payment_charge = $this->config->get('toyyibpay_payment_charge');
        $extra_email = $this->config->get('toyyibpay_extra_email');
		$typLog_file = $this->config->get('toyyibpay_log_file');
		$typLog_api = $this->config->get('toyyibpay_log_api');
		
		if (!in_array($typLog_file,array('1','2'))) {
			$typLog_file = 0;
			$typLog_api = '';
		}
		
		if (empty($bill_name)) {
			$bill_name = empty($_SESSION['bill_name']) ? $_SESSION['name'] : $_SESSION['bill_name'];
		}
		
        $name = $_SESSION['name'];
        $email = $_SESSION['email'];
        $description = $_SESSION['description'];
        $mobile = $_SESSION['mobile'];
        $ext_ref_no = $_SESSION['order_id'];
        $amount = preg_replace("/[^0-9.]/", "", $_SESSION['amount']) * 100;
        $redirect_url = $_SESSION['redirect_url'];
        $callback_url = $_SESSION['callback_url'];
		
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        unset($_SESSION['bill_name']);
        unset($_SESSION['description']);
        unset($_SESSION['mobile']);
        unset($_SESSION['order_id']);
        unset($_SESSION['amount']);
        unset($_SESSION['redirect_url']);
        unset($_SESSION['callback_url']);
		
		$parameter = array(
			'userSecretKey'				=> trim($api_key),
			'categoryCode'				=> trim($category_code),
			'billName'					=> mb_substr($bill_name, 0, 30),
			'billDescription'			=> mb_substr($description, 0, 100),
			'billPriceSetting'			=> 1,
			'billPayorInfo'				=> 1, 
			'billAmount'				=> $amount, 
			'billReturnUrl'				=> $redirect_url,
			'billCallbackUrl'			=> $callback_url,
			'billExternalReferenceNo' 	=> $ext_ref_no,
			'billTo'					=> mb_substr(trim($name), 0, 50),
			'billEmail'					=> trim($email),
			'billPhone'					=> trim($mobile),
			'billSplitPayment'			=> 0,
			'billSplitPaymentArgs' 		=> '',
			'billPaymentChannel' 		=> ($payment_channel=='' ? 2 : $payment_channel)
		);

        if (!empty(trim($extra_email))) {
            $parameter['billContentEmail'] = trim($extra_email);
        }
        if (!empty(trim($payment_charge))) {
            $parameter['billChargeToCustomer'] = trim($payment_charge);
        }
        if (empty($parameter['billName'])) {
            $parameter['billName'] =  'Payment from Opencart 1.5';
        }
        if (empty($parameter['billTo'])) {
            $parameter['billTo'] =  'Payer Name Unavailable';
        } 
        if (empty($parameter['billEmail'])) {
            $parameter['billEmail'] = 'noreply@toyyibpay.com';
        }
        if (empty($parameter['billPhone'])) {
            $parameter['billPhone'] = '60';
        } 

        $toyyibpay = new ToyyibPayAPI(trim($api_key),$api_host,$typLog_file,$typLog_api);
		$createBill = $toyyibpay->createBill($parameter);

		if( $createBill['respStatus']===false ) {
            $toyyibpay->throwException( $createBill['respData'] );
            exit;
		}
		
		if ( empty($createBill['respData']['BillCode']) ) {
			//BillCode Not Exist
			$toyyibpay->throwException( 'ERROR : BillCode not return!' );
            exit;
		}
		
        header('Location: ' . $createBill['respData']['BillURL'] );
		
    }

    public function return_ipn()
    {
        $this->load->model('checkout/order');
        /*
         * Get Data. Die if input is tempered or X Signature not enabled
         */
		 
        $api_key = $this->config->get('toyyibpay_api_key');
		$api_host = $this->config->get('toyyibpay_api_environment');
		$typLog_file = $this->config->get('toyyibpay_log_file');
		$typLog_api = $this->config->get('toyyibpay_log_api');
		if (!in_array($typLog_file,array('1','2'))) {
			$typLog_file = 0;
			$typLog_api = '';
		}
		
		$toyyibpay = new ToyyibPayAPI(trim($api_key),$api_host,$typLog_file,$typLog_api);
		$data = $toyyibpay->getTransactionData();
		
        $orderid = $data['order_id'];
        $status = $data['paid'];
        $amount = $data['amount'];
        $orderHistNotes = "Redirect: " . date(DATE_ATOM) . " BillCode:" . $data['billcode'] . " Status:" . $data['status_name'];
		
        $order_info = $this->model_checkout_order->getOrder($orderid); // orderid
        $order_status_id = $this->config->get('toyyibpay_order_status_id');

        if ($status) {
            $order_status_id = $this->config->get('toyyibpay_success_status_id');
            $goTo = $this->url->link('checkout/success');
			$this->cart->clear();
        } else {
            $order_status_id = $this->config->get('toyyibpay_failed_status_id');
            $goTo = $this->url->link('checkout/cart');
        }

        if (!$order_info['order_status']) {
            $this->model_checkout_order->confirm($orderid, $order_status_id);
        }

        /*
         * Prevent same order status id from adding more than 1 update
         */
        if ($order_status_id != $order_info['order_status_id']) {
            $this->model_checkout_order->update($orderid, $order_status_id, $orderHistNotes, false);
        }
        //echo '<pre>' . print_r($order_info, true) . '</pre>';

        if (!headers_sent()) {
            header('Location: ' . $goTo);
        } else {
            echo "If you are not redirected, please click <a href=" . '"' . $goTo . '"' . " target='_self'>Here</a><br />"
            . "<script>location.href = '" . $goTo . "'</script>";
        }
    }
    /*     * ***************************************************
     * Callback with IPN(Instant Payment Notification)
     * **************************************************** */

    public function callback_ipn()
    {
        $this->load->model('checkout/order');
        /*
         * Get Data. Die if input is tempered or X Signature not enabled
         */
		 
        $api_key = $this->config->get('toyyibpay_api_key');
		$api_host = $this->config->get('toyyibpay_api_environment');
		
		$typLog_file = $this->config->get('toyyibpay_log_file');
		$typLog_api = $this->config->get('toyyibpay_log_api');
		if (!in_array($typLog_file,array('1','2'))) {
			$typLog_file = 0;
			$typLog_api = '';
		}
		
        $toyyibpay = new ToyyibPayAPI(trim($api_key),$api_host,$typLog_file,$typLog_api);
		$data = $toyyibpay->getTransactionData();
		
        $tranID = $data['billcode'];
        $orderid = $data['order_id'];
        $status = $data['paid'];
        //$amount = $data['amount'];
		$orderHistNotes = "Callback: " . date(DATE_ATOM) . " BillCode:" . $data['billcode'] . " Status:" . $data['status_name'];
		
        $order_info = $this->model_checkout_order->getOrder($orderid); // orderid
        $order_status_id = $this->config->get('toyyibpay_order_status_id');

        if ($status) {
            $order_status_id = $this->config->get('toyyibpay_success_status_id');
            $goTo = $this->url->link('checkout/success');
			$this->cart->clear();
        } else {
            $order_status_id = $this->config->get('toyyibpay_pending_status_id');
            $goTo = $this->url->link('checkout/cart');
        }

        if (!$order_info['order_status']) {
            $this->model_checkout_order->confirm($orderid, $order_status_id);
        }

        /*
         * Prevent same order status id from adding more than 1 update
         */
        if ($order_status_id != $order_info['order_status_id']) {
            $this->model_checkout_order->update($orderid, $order_status_id, $orderHistNotes, false);
        }
        exit('Callback Success');
    }
	
}
