<?php
/**
 * ToyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author ToyyibPay Team
 * @LastUpdated on 202004142330
 */
 
class ControllerExtensionPaymentToyyibpay extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/toyyibpay');
        $data = array(
            'button_confirm' => $this->language->get('button_confirm'),
            'action' => $this->url->link('extension/payment/toyyibpay/proceed', '', true)
        );

        return $this->load->view('extension/payment/toyyibpay', $data);
    }

    public function proceed()
    {
        $api_host			= $this->config->get('toyyibpay_api_environment_value');
        $api_key			= $this->config->get('toyyibpay_api_key_value');
        $category_code 		= $this->config->get('toyyibpay_category_code_value');
		$bill_name			= $this->config->get('toyyibpay_bill_name_value');
        $payment_channel 	= $this->config->get('toyyibpay_payment_channel_value');
        $payment_charge 	= $this->config->get('toyyibpay_payment_charge_value');
        $extra_email 		= $this->config->get('toyyibpay_extra_email_value');
		
		$typLog_file	= $this->config->get('toyyibpay_log_file_value');
		$typLog_api		= $this->config->get('toyyibpay_log_api_value');
		if (!in_array($typLog_file,array('1','2'))) {
			$typLog_file = 0;
			$typLog_api = '';
		}

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $data['prod_name'][] = $product['name'];
			$data['prod_desc'][] = $product['name'] . " x " . $product['quantity'];
        }
		
		if (empty($bill_name)) {
			$bill_name = empty(implode($data['prod_name'])) ? trim($order_info['firstname'] . ' ' . $order_info['lastname']) : implode($data['prod_name']);
		}

		$parameter = array(
			'userSecretKey'				=> trim($api_key),
			'categoryCode'				=> trim($category_code),
			'billName'					=> mb_substr($bill_name, 0, 30),
			'billDescription' 			=> mb_substr("Order " . $this->session->data['order_id'] . " - " . implode($data['prod_desc']), 0, 200),
			'billPriceSetting'			=> 1,
			'billPayorInfo'				=> 1, 
			'billAmount'				=> strval($amount * 100), 
			'billReturnUrl'				=> $this->url->link('extension/payment/toyyibpay/return_ipn', '', true),
			'billCallbackUrl'			=> $this->url->link('extension/payment/toyyibpay/callback_ipn', '', true),
			'billExternalReferenceNo'	=> $order_info['order_id'],
			'billTo'					=> trim($order_info['firstname'] . ' ' . $order_info['lastname']),
			'billEmail'					=> trim($order_info['email']),
			'billPhone'					=> trim($order_info['telephone']),
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
            $parameter['billName'] =  'Peyment from Opencart 2.3';
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
		
		$this->cart->clear();

        header('Location: ' . $createBill['respData']['BillURL'] );
		
    }

    public function return_ipn()
    {
		$api_host		= $this->config->get('toyyibpay_api_environment_value');
        $api_key		= $this->config->get('toyyibpay_api_key_value');
		$typLog_file	= $this->config->get('toyyibpay_log_file_value');
		$typLog_api		= $this->config->get('toyyibpay_log_api_value');
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

        if ($status) {
            $order_status_id = $this->config->get('toyyibpay_completed_status_id');
			$goTo = $this->url->link('checkout/success');
			$this->cart->clear();
        } else {
            $order_status_id = $this->config->get('toyyibpay_failed_status_id');
			$goTo = $this->url->link('checkout/checkout');
        }
		
        if (!$order_info['order_status_id'])
            $this->model_checkout_order->addOrderHistory($orderid, $order_status_id, $orderHistNotes, false);
        else {
            /*
             * Prevent same order status id from adding more than 1 update
             */
            if ($order_status_id != $order_info['order_status_id'])
                $this->model_checkout_order->addOrderHistory($orderid, $order_status_id, $orderHistNotes, false);
        }

        if (!headers_sent()) {
            header('Location: ' . $goTo);
        } else {
            echo "If you are not redirected, please click <a href=" . '"' . $goTo . '"' . " target='_self'>Here</a><br />"
            . "<script>location.href = '" . $goTo . "'</script>";
        }

        exit();
    }
    /*     * ***************************************************
     * Callback with IPN(Instant Payment Notification)
     * **************************************************** */

    public function callback_ipn()
    {
        $this->load->model('checkout/order');
		
		$api_host		= $this->config->get('toyyibpay_api_environment_value');
        $api_key		= $this->config->get('toyyibpay_api_key_value');
		$typLog_file	= $this->config->get('toyyibpay_log_file_value');
		$typLog_api		= $this->config->get('toyyibpay_log_api_value');
		if (!in_array($typLog_file,array('1','2'))) {
			$typLog_file = 0;
			$typLog_api = '';
		}

		$toyyibpay = new ToyyibPayAPI(trim($api_key),$api_host,$typLog_file,$typLog_api);
		$data = $toyyibpay->getTransactionData();	
		
		$orderid = $data['order_id'];
        $status = $data['paid'];
        $amount = $data['amount'];
        $orderHistNotes = "Callback: " . date(DATE_ATOM) . " BillCode:" . $data['billcode'] . " Status:" . $data['status_name'];
        $order_info = $this->model_checkout_order->getOrder($orderid); // orderid

        if ($status) {
            $order_status_id = $this->config->get('toyyibpay_completed_status_id');
			$this->cart->clear();
        } else {
            $order_status_id = $this->config->get('toyyibpay_pending_status_id');
        }


		if (!$order_info['order_status_id'])
            $this->model_checkout_order->addOrderHistory($orderid, $order_status_id, $orderHistNotes, $notify = false);
        else {
            /*
             * Prevent same order status id from adding more than 1 update
             */
            if ($order_status_id != $order_info['order_status_id'])
                $this->model_checkout_order->addOrderHistory($orderid, $order_status_id, $orderHistNotes, $notify = false);
        }

        exit;
    }
	
}


class ToyyibPayAPI
{
	private $api_key;
	private $category_code;

	private $process;
	public $is_production;
	public $url;

	public $header;

	const TIMEOUT = 10; //10 Seconds
	const PRODUCTION_URL = 'https://toyyibpay.com/';
	const STAGING_URL = 'https://dev.toyyibpay.com/';
	
	private $default_category_name = "Payment For Purchase From Opencart";
	
	private $logging_api = true;
	private $logging_api_url;
	private $logging_file; //0=Disabled, 1=All Api Xtvt, 2=Error Only
	private $logging_dir = DIR_LOGS ; //set log file directory, set function line 388 base on opencart
	private $max_log_size = 12; //max log size in Mb

	public function __construct($api_key,$is_production,$logging_file=0,$logging_api_url='')
	{
		if( $is_production=='' ) $this->is_production = true;
		else $this->is_production = $is_production===true||$is_production===1||$is_production==='1' ? true : false;
		
		$this->api_key = $api_key;
		$this->header = $api_key . ':';
		
		$this->logging_file = $logging_file;
		$this->logging_api_url = $logging_api_url;
	}
	
	public function setMode()
	{
		if ($this->is_production) {
			$this->url = self::PRODUCTION_URL;
		} else {
			$this->url = self::STAGING_URL;
		}
		return $this;
	}

	public function throwException($message)
	{
		//echo '<pre>'; 
		//echo "Parameter->\n"; //print_r($parameter);
		//echo '</pre><hr/>';	
		
		echo "<script> alert('" .trim(addslashes($message)). "'); </script>"; 
		echo "<h3>" .addslashes($message). "</h3>"; 
		
		if( $this->logging_file==1 || $this->logging_file==2 ) { 
			$this->logToFile($message);
		}
		
		//throw new Exception($message);
	}

	public function createCategory($title='', $description='')
	{
		if($title=='') $title = $this->default_category_name;
		
		$data = array(
			'userSecretKey' => $this->api_key,
			'catname' => $title,
			'catdescription' => $description
		);
		$this->setActionURL('CREATECATEGORY'); 
		$result = $this->toArray($this->submitAction($data)); 
		$category = $result['respData'];
		
		if(is_array($category[0])) {
			return $category[0]['CategoryCode'];
		}
		else {
			return $category['CategoryCode'];
		}
	}
	
	public function checkCategory($id)
	{
		$data = array(
			'userSecretKey' => $this->api_key,
			'categoryCode' => $id
		);
		$this->setActionURL('CHECKCATEGORY'); 
		$result = $this->toArray($this->submitAction($data)); 
		
		if(is_array($result['respData'][0])) {
			return $result['respData'][0]['categoryStatus'];
		}
		else {
			return $result['respData']['categoryStatus'];
		}
	}
	
	public function createBill($parameter)
	{
		/* Email or Mobile must be set */
		if (empty($parameter['billEmail']) && empty($parameter['billPhone'])) {
			$this->throwException("Email or Mobile must be set!");
		}

		/* Validate Mobile Number first */
		if (!empty($parameter['billPhone'])) {
			/* Strip all unwanted character */
			$parameter['billPhone'] = preg_replace('/[^0-9]/', '', $parameter['billPhone']);

			/* Add '6' if applicable */
			$parameter['billPhone'] = $parameter['billPhone'][0] === '0' ? '6'.$parameter['billPhone'] : $parameter['billPhone'];

			/* If the number doesn't have valid formatting, reject it */
			/* The ONLY valid format '<1 Number>' + <10 Numbers> or '<1 Number>' + <11 Numbers> */
			/* Example: '60141234567' or '601412345678' */
			if (!preg_match('/^[0-9]{11,12}$/', $parameter['billPhone'], $m)) {
				$parameter['billPhone'] = '';
			}
		}
		
		/* Check Category Code that has been entered is valid or not. If invalid, unset categoryCode */
		if ( isset($parameter['categoryCode']) ) {
			$status = $this->checkCategory($parameter['categoryCode']);
			if (!$status) unset($parameter['categoryCode']);
		}

		/* If not set, get Category Code */
		// if (!isset($parameter['categoryCode'])) {
		if(empty($parameter['categoryCode'])) {
			$parameter['categoryCode'] = $this->createCategory();
		}
		
		//Last Check
		if (empty($parameter['categoryCode'])) {
			$this->throwException("Category Code Not Found! ");
		}
		
		//Create Verification Code
		$vcode = md5($parameter['userSecretKey'].$parameter['billExternalReferenceNo'].$parameter['billAmount']);
		$parameter['billReturnUrl'] = $this->setUrlQuery($parameter['billReturnUrl'],array('vc'=>$vcode));
		$parameter['billCallbackUrl'] = $this->setUrlQuery($parameter['billCallbackUrl'],array('vc'=>$vcode));
		
		/* Create Bills */
		$this->setActionURL('CREATEBILL');	
		$bill = $this->toArray($this->submitAction($parameter)); 
		$billdata = $this->setPaymentURL($bill); 
		
		return $billdata;
	}
	
	public function setPaymentURL($bill)
	{		
		$return = $bill;
		if( $bill['respStatus'] ) {
			if( isset($bill['respData'][0]['BillCode']) ) {
				$this->setActionURL('PAYMENT', $bill['respData'][0]['BillCode'] ); 
				$bill['respData'][0]['BillURL'] = $this->url;
			}
			$return = array('respStatus'=>$bill['respStatus'], 'respData'=>$bill['respData'][0]);
			
			$LogDataCheck = array(
				'RequestData'=> json_encode($bill),
				'ResponseData'=> json_encode($return),
				'ResponseStatus'=> $bill['respStatus']
			);
			$this->createTypLog($LogDataCheck);
		}
		
		return $return;
	}
	
	public function checkBill($parameter)
	{
		$this->setActionURL('CHECKBILL');
		$checkData = $this->toArray($this->submitAction($parameter)); 
		$checkData['respData'] = $checkData['respData'][0];
		
		return $checkData;	
	}
	
	public function deleteBill($parameter)
	{
		$this->setActionURL('DELETEBILL');
		$checkData = $this->toArray($this->submitAction($parameter)); 
		$checkData['respData'] = $checkData['respData'][0];
		
		return $checkData;
	}

	public function setUrlQuery($url,$data)
	{
		if (!empty($url)) {
			if( count( explode("?",$url) ) > 1 )  
				$url = $url .'&'. http_build_query($data);
			else  
				$url = $url .'?'. http_build_query($data);
		}
		return $url;
	}

	public function getTransactionData()
	{
		if (isset($_GET['billcode']) && isset($_GET['transaction_id']) && isset($_GET['order_id']) && isset($_GET['status_id'])) {

			$data = array(
				'status_id' => $_GET['status_id'],
				'billcode' => $_GET['billcode'],
				'order_id' => $_GET['order_id'],
				'msg' => $_GET['msg'],
				'transaction_id' => $_GET['transaction_id']
			);
			$type = 'redirect';
			
		} elseif ( isset($_POST['refno']) && isset($_POST['status']) && isset($_POST['amount']) ) {

			$data = array(
				'status_id' => $_POST['status'],
				'billcode' => $_POST['billcode'],
				'order_id' => $_POST['order_id'],
				'amount' => $_POST['amount'],
				'reason' => $_POST['reason'],
				'transaction_id' => $_POST['refno']
			);
			$type = 'callback';
			
		} else {
			return false;
		}
		
		$checkAction = ($type=='redirect'?'RETURNREDIRECT':($type=='callback'?'RETURNCALLBACK':''));
		
		$LogDataCheck = array(
			'RequestData'=> json_encode(array('GET'=>$_GET,'POST'=>$_POST),true),
			'ResponseData'=> json_encode($data),
			'ResponseStatus'=> true
		);
		$this->createTypLog($LogDataCheck,$checkAction);
		
		if( $type === 'redirect' ) {
			//check bill
			$parameter = array(
				'billCode' => $data['billcode'],
				'billExternalReferenceNo' => $data['order_id']
			);
			$checkbill = $this->checkBill($parameter);
			if( $checkbill['respStatus'] ) {
				if($checkbill['respData']['billpaymentStatus'] != $data['status_id']) {
					$data['status_id'] = $checkbill['respData']['billpaymentStatus'];
				}
				$data['amount'] = $checkbill['respData']['billpaymentAmount'];
			}
			else {
				//$this->throwException( $checkbill['respData'] );
			}
		}
		
		$data['paid'] = $data['status_id'] === '1' ? true : false; /* Convert paid status to boolean */
		
		if( $data['status_id']=='1' ) $data['status_name'] = 'Success';
		else if( $data['status_id']=='2' ) $data['status_name'] = 'Pending';
		else $data['status_name'] = 'Unsuccessful';
		
		$data['vcode'] = $_GET['vc'];
		$amount = preg_replace("/[^0-9.]/", "", $data['amount']) * 100;
		$vcode = md5( $this->api_key . $data['order_id'] . $amount );
		
		if($data['vcode'] !== $vcode) {
			$this->throwException('Verification Code Mismatch!');
		}
		
		$data['type'] = $type;
		return $data;

	}

	public function setActionURL($action, $id = '')
	{
		$this->setMode();
		$this->action = $action;
		
		if ($this->action == 'PAYMENT') {
			$this->url .= $id;
		}
		else if ($this->action == 'CREATECATEGORY') {
			$this->url .= 'index.php/api/createCategory';
		}
		else if ($this->action == 'CHECKCATEGORY') {
			$this->url .= 'index.php/api/getCategoryDetails';
		}
		else if ($this->action == 'CREATEBILL') {
			$this->url .= 'index.php/api/createBill';
		}
		else if ($this->action == 'CHECKBILL') {
			$this->url .= 'index.php/api/getBillTransactions';
		}
		else if ($this->action == 'DELETEBILL') { //belum ada
			$this->url .= 'index.php/api/getBillTransactions';
		}
		else {
			$this->throwException('URL Action not exist');
		}
		
		return $this;
	}
	
	public function submitAction($data='')
	{		
		$this->process = curl_init();
		curl_setopt($this->process, CURLOPT_HEADER, 0);
		curl_setopt($this->process, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->process, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->process, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->process, CURLOPT_TIMEOUT, self::TIMEOUT);
		curl_setopt($this->process, CURLOPT_USERPWD, $this->header);
		
		curl_setopt($this->process, CURLOPT_URL, $this->url);
		curl_setopt($this->process, CURLOPT_POSTFIELDS, http_build_query($data));
		if ($this->action == 'DELETE') {
			curl_setopt($this->process, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		$response = curl_exec($this->process);
		$httpStatusCode  = curl_getinfo($this->process, CURLINFO_HTTP_CODE);
		curl_close($this->process);
		
		if( $httpStatusCode==200 ) {
			$respStatus = true;
			if( trim($response)=='[FALSE]' ) {
				$respStatus = false;
				$response = 'API_ERROR '. trim($response) .' : Please check your request data with Toyyibpay Admin';
			}
			else if( trim($response)=='[KEY-DID-NOT-EXIST]' ) {
				$respStatus = false;
				$response = 'API_ERROR '. trim($response) .' : Please check your api key.';
			}
			
			if( trim($response)=='' ) {
				$respStatus = false;
				$response = 'API_ERROR : No Response Data From Toyyibpay.';
			}
		}
		else {
			$respStatus = false;
			$response = 'API_ERROR '. $httpStatusCode .' : Cannot Connect To ToyyibPay Server.';
		}			
		
		if( $respStatus ) {
			$responseArr = $this->toArray($response); 
			if( $responseArr['status']=='error' ) {
				$respStatus = false;
				$response = 'ERROR : '. $responseArr['msg'];
			}
		}
		
		//$return = json_decode($response, true);
		$return = array('respStatus'=>$respStatus, 'respData'=>$response);
		
		$LogDataCheck = array(
			'RequestData'=> json_encode($data),
			'ResponseHttpStatus'=> (is_array($httpStatusCode) ? json_encode($httpStatusCode) : $httpStatusCode),
			'ResponseStatus'=> $respStatus,
			'ResponseData'=> (is_array($response) ? json_encode($response) : $response)
		);
		$this->createTypLog($LogDataCheck);
		
		return $return;
	}

	public function logToFile($data) 
	{
		if( $this->logging_file ) {
			if(file_exists($this->logging_dir . 'toyyibpay_payment.log')) {
				if(filesize($this->logging_dir . 'toyyibpay_payment.log') > ($this->max_log_size * 1000000)) {
					rename($this->logging_dir . 'toyyibpay_payment.log', $this->logging_dir . 'toyyibpay_payment_old' . date('Y-m-d_H-i-s') . '.log');
				}
			}
			$this->logger = new \Log('toyyibpay_payment.log'); //Function Log from Opencart
			$this->logger->write($data);
		}
	}
	
	public function createTypLog($data='',$action='',$url='')
	{		
		$param = array(
			'ACTION'=> ( $action=='' ? $this->action : $action ),
			'URL'=> ( $url=='' ? $this->url : $url )
		);
		if( is_array($data) ) $param = array_merge($param, $data);
		else $param['PARAMETER'] = json_encode($data);
		
		//Save To Log File
		if( $this->logging_file==1 ) {
			$this->logToFile($param);
		}
		else if( $this->logging_file==2 ) {
			if( $data['ResponseStatus'] !== true ) {
				$this->logToFile($param);
			}
		}
		
		//Send Log Data To API
		if( $this->logging_api && $this->logging_api_url ) {
			
			$checkLog = curl_init();
			curl_setopt($checkLog, CURLOPT_HEADER, 0);
			curl_setopt($checkLog, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($checkLog, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($checkLog, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($checkLog, CURLOPT_TIMEOUT, 3);
			curl_setopt($checkLog, CURLOPT_USERPWD, $this->header);
			curl_setopt($checkLog, CURLOPT_URL, $this->logging_api_url);
			curl_setopt($checkLog, CURLOPT_POST, 1);
			curl_setopt($checkLog, CURLOPT_POSTFIELDS, $param);

			$return = curl_exec($checkLog);
			$info = curl_getinfo($checkLog, CURLINFO_HTTP_CODE);
			curl_close($checkLog);
			
			return json_decode($return, true);
		}
		else {
			return true;
		}
	}
	
	public function toArray($json)
	{
		if( is_string($json['respData']) && is_array(json_decode($json['respData'],true)) ) { //check json ke x
			return array('respStatus'=>$json['respStatus'], 'respData'=>json_decode($json['respData'],true));
		} else {
			return array('respStatus'=>$json['respStatus'], 'respData'=>$json['respData']);
		}
	}


}//close class ToyyibPayAPI
