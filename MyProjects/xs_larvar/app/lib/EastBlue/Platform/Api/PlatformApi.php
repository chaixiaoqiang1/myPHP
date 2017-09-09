<?php namespace EastBlue\Platform\Api;

/*
 * 网站平台与支付平台 API 接口
 * 涉及到两套签名方式，添加 API 的时候请注意签名方式
 *
 */

use \Curl;
use \Response;
use \Log;
use \Auth;
use \Session;

class PlatformApi implements PlatformApiInterface{

	protected $api_url = '';
	protected $api_key = '';
	protected $api_secret_key = '';
	protected $response = '';

	public function __construct()
	{
		
	}

	public function connect($url, $api_key, $api_secret_key)
	{
		$this->api_url = $url;
		$this->api_key = $api_key;
		$this->api_secret_key = $api_secret_key;	
		return $this;
	}

	protected function getResponse($method, $params)
	{
		$method = $this->api_url . $method;
		//这里打印一条日志，记录所有调用接口的信息
		if(Auth::check()){
			Log::info('PlatformApi--Username:'.Auth::user()->username.',game_id:'.Session::get('game_id').',method:'.$method.',params:'.var_export($params, true));
		}else{
	        Log::info('PlatformApi--method:'.$method.',params:'.var_export($params, true));
	    }
		$response = Curl::url($method)->postFields($params)->post();
        ////////////////////////////////取消注释可打印  post 和 response 的详细信息。
        /*if(($this->api_url . '/pay_api/update_payment_method') == $method) {
            foreach ($response as $k => $v) {
                $data_res[$k] = $v;
            }
            Log::info("post method:" . $method . "-----post params:" . var_export($params, true) . "-----post response:" . var_export($data_res, true));
        }*/
        /*if(($this->api_url . '/pay_api/common_refill') == $method) {
            foreach ($response as $k => $v) {
                $data_res[$k] = $v;
            }
            Log::info("post method:" . $method . "-----post params:" . var_export($params, true) . "-----post response:" . var_export($data_res, true));
        }*/
        /////////////////////////////////////////////////////////////////////////

		$this->response = $response;
		return $this->response;	
	}

    public function test_home_api($method,$params){
		$method = $this->api_url . $method;
	    // var_dump($method);
		// var_dump($params);die();
		$response = Curl::url($method)->postFields($params)->post();
 		//Log::info(var_export($response, true));
		$this->response = $response;
		return $this->response;	
        
    }
	public function sendResponse()
	{
		$http_code = $this->response->http_code;
		$body = $this->response->body;

		if (isset($body->error) && $body->error > 0) {
			$this->response->code = $body->error;
			$this->response->error = $body->error_description;
			return Response::json($this->response, $http_code);
		} else if($http_code >= 500){
			return Response::json(array('error'=>'系统内部错误，请联系技术'), 403);
		} else if (isset($body->code) && isset($body->error)) {
			return Response::json($body, $http_code);
		}else{
			return Response::json($body);
		}
	}

	private function makeSignMD5($params)
	{
		$params['client_secret'] = $this->api_secret_key;
		uksort($params, 'strcmp');
		return md5(http_build_query($params));
	}

	/*
	 * 接口方法
	 *
	 */
		
	public function editUserPassword($uid, $new_password)
	{
		$method = '/home_api/change_password';
		$params = array(
			'uid' => $uid,
			'password' => $new_password,
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function setLoginMasterByEmail($email, $type)
	{
		$method = '/home_api/set_login_master';

		$params = array(
			'login_email' => $email,
			'type'       => $type,
			'time'       => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function setLoginMasterByUID($uid, $type)
	{
		$method = '/home_api/set_login_master';

		$params = array(
			'uid' => $uid,
			'type'       => $type,
			'time'       => time(),
			'client_id' => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function addNewPaymentServer($params)
	{
		$keys = array(
			'server_id',
			'server_name',
			'server_ip',
			'server_port',
			'game_id',
			'dir_id', 
			'on_recharge',
			'use_for_month_card',
			'server_internal_id',
		);
		
		foreach ($params as $k => $v) {
			if (!in_array($k, $keys)) {
				unset($params[$k]);
			}
		}
		$params['client_id'] = $this->api_key;
		$params['time'] = time();
		$method = '/pay_api/new_server';


		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function updatePaymentServer($params)
	{
		$keys = array(
			'server_name',
			'server_ip',
			'server_port',
			'game_id',
			'dir_id', 
			'on_recharge',
			'use_for_month_card',
			'server_id',
			'server_internal_id',
		);
		
		foreach ($params as $k => $v) {
			if (!in_array($k, $keys)) {
				unset($params[$k]);
			}
		}
		$params['client_id'] = $this->api_key;
		$params['time'] = time();
		$method = '/pay_api/update_server';
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function addNewPlatformServer($params)
	{
		$keys = array(
			'game_id',
			'server_track_name',
			'server_name',
			'version',
			'game_path',
			'source_path',
			'xload_path',
			'battle_report_path',
			'server_ip',
			'server_port',
			'server_internal_id',
			'api_server_ip',
			'api_server_port',
			'dir_id',
			'is_server_on',
			'tp_server_id',
			'zoneid',
			'open_server_time',
		);

		foreach ($params as $k => $v) {
			if (!in_array($k, $keys)) {
				unset($params[$k]);
			}
		}
		$params['client_id'] = $this->api_key;	
		$params['time'] = time();
		$method = '/home_api/new_server';
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function updatePlatformServer($params)
	{
		$keys = array(
			'server_track_name',
			'server_name',
			'version',
			'game_path',
			'source_path',
			'xload_path',
			'battle_report_path',
			'server_ip',
			'server_port',
			'is_server_on',
			'server_internal_id',
			'api_server_ip',
			'api_server_port',
			'dir_id',
			'server_id',
			'tp_server_id',
			'zoneid',
			'open_server_time'
		);

		foreach ($params as $k => $v) {
			if (!in_array($k, $keys)) {
				unset($params[$k]);
			}
		}
		$params['client_id'] = $this->api_key;
		$params['time'] = time();
		$method = '/home_api/update_server';
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function getPlatformServer($platform_server_id)
	{
		$params = array(
			'server_id' => $platform_server_id,
			'time' => time(),
			'client_id' => $this->api_key
		);

		$method = '/home_api/get_server';
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;

		return $this->getResponse($method, $params);
	}

	public function getPaymentServer($platform_server_id)
	{
		$params = array(
			'server_id' => $platform_server_id,
			'time' => time(),
			'client_id' => $this->api_key
		);

		$method = '/pay_api/get_server';
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}


	public function createRefundOrder($order_id, $refund_amount, $currency_code)
	{
		$method = '/pay_api/create_refund_order';
		$params = array(
			'payment_order_id' => $order_id,
			'refund_amount' => $refund_amount,
			'currency' => $currency_code,
			'time' => time(),
			'client_id' => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
	public function caozuoDelayOrder($order)
	{
		$method = '/pay_api/update_delay_order';
		$params = array(
			'order_sn' 	  => $order['order_sn'],
			'user_id'  	  => $order['user_id'],
			'deal_status' => $order['deal_status'],
			'code'		  => $order['code'],
			'time'        => time(),
			'client_id'   => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
	public function giveYuanbaoForMycard($order)
	{
		$method = '/pay_api/replenish_order';
		$params = array(
			'payment_order_id'       => $order['order_id'],
			'pay_amount'             => $order['pay_amount'],
			'basic_yuanbao_amount'   => $order['basic_yuanbao_amount'],
			'extra_yuanbao_amount'   => $order['extra_yuanbao_amount'],
			'huodong_yuanbao_amount' => $order['huodong_yuanbao_amount'],
			'yuanbao_amount'         => $order['yuanbao_amount'],
			'tradeseq' 				 => $order['tradeseq'],
			'mycard_id' 			 => $order['mycard_id'],
			'time'                   => time(),
			'client_id' => $this->api_key
		);
		if(isset($order['giftbag_id'])){
			$params['giftbag_id'] = $order['giftbag_id'];
		}
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function giveYuanbaoForOther($order)
	{
		$method = '/pay_api/common_refill';
		$params = array(
			'order_id'               => $order['order_id'],
			'order_sn'				 => $order['order_sn'],
			'tradeseq'               => $order['tradeseq'],
			'pay_amount'             => $order['pay_amount'],
			'basic_yuanbao_amount'   => $order['basic_yuanbao_amount'],
			'extra_yuanbao_amount'   => $order['extra_yuanbao_amount'],
			'huodong_yuanbao_amount' => $order['huodong_yuanbao_amount'],
			'yuanbao_amount'         => $order['yuanbao_amount'],
			'time'                   => time(),
			'client_id' => $this->api_key
		);
		if(isset($order['giftbag_id'])){
			$params['giftbag_id'] = $order['giftbag_id'];
		}
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function updateOrderInfo($order)
	{
		$method = '/pay_api/update_order_data';
		$params = array(
			'order_id'               => $order['order_id'],
			'tradeseq'               => $order['tradeseq'],
			'pay_amount'             => $order['pay_amount'],
			'basic_yuanbao_amount'   => $order['basic_yuanbao_amount'],
			'extra_yuanbao_amount'   => $order['extra_yuanbao_amount'],
			'huodong_yuanbao_amount' => $order['huodong_yuanbao_amount'],
			'yuanbao_amount'         => $order['yuanbao_amount'],
			'time'                   => time(),
			'order_status'           => 3,
			'offer_yuanbao'          => 1,
			'get_payment'            => 1,
			'offer_time'             => time(),
			'client_id' => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function executeRefund($dispute_id, $payment_id, $currency, $amount, $reason_id)
	{
		$method = '/pay_api/execute_refund';
		$reasons = array(
			1 => "MALICIOUS_FRAUD",
			2 => "FRIENDLY_FRAUD",
			3 => "CUSTOMER_SERVICE",
		);
		$params = array(
			'dispute_id' => $dispute_id,
			'payment_id' => $payment_id,
			'currency'   => $currency,
			'amount'     => $amount,
			'reason'     => $reasons[$reason_id],
			'time'       => time(),
			'client_id' => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function changeDispute($dispute_id, $amount, $type)
	{
		$method = '/pay_api/change_dispute';
		$params = array(
			'dispute_id' => (int)$dispute_id,
			'amount'     => (float)$amount,
			'type'       => (int)$type,
			'time'       => time(),
			'client_id' => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function createExchangeRates($exchange, $currency_id, $username)
	{
		$method = '/pay_api/add_exchange_rate';
		$params = array(
			'exchange' => $exchange,
			'type'     => $currency_id,
			'admin'    => $username,
			'timeline' => date("Y-m-d H:i:s"),
			'time'     => time(),
			'client_id' => $this->api_key
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function upgradeAnonymous($email, $uid, $pwd)
	{
		$method = '/home_api/anony_upgrade';
		$params = array(
			'login_email' => $email,
			'uid' => $uid,
			'password' => $pwd,
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function createNewOrder($order)
	{
		$method = '/pay_api/add_order';
		$params = array(
			'server_id' => $order['platform_server_id'],
			'game_id' => $order['game_id'],
			'pay_user_id' => $order['pay_user_id'],
			'currency' => $order['currency'],
			'pay_amount' => $order['pay_amount'],
			'basic_yuanbao_amount' => $order['basic_yuanbao_amount'],
			'extra_yuanbao_amount' => $order['basic_yuanbao_amount'],
			'huodong_yuanbao_amount' => $order['huodong_yuanbao_amount'],
			'yuanbao_amount' => $order['yuanbao_amount'],
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
	public function createOrder($order)
	{
		$method = '/pay_api/add_order_new';
		$order['time'] = time();
		$order['client_id'] = $this->api_key;
		$sign = $this->makeSignMD5($order);
		$order['sign'] = $sign;
		return $this->getResponse($method, $order);
	}
	public function bindEmail($old_login_email, $login_email)
	{
		$method = '/home_api/bind_email';
		$params = array(
			'old_login_email' => $old_login_email,
			'login_email' => $login_email,
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function addPlatformPayType($params){
		$method = '/pay_api/add_pay_type';
		$params = array(
		        'pay_type_id' => $params['pay_type_id'],
		        'pay_type_name' => $params['pay_type_name'],
		        'company' => $params['company'],
		        'time' => time(),
		        'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
	public function updatePlatformPayType($params){
		$method = '/pay_api/update_pay_type';
		$params = array(
		        'id' => $params['id'],
		        'pay_type_id' => $params['pay_type_id'],
		        'pay_type_name' => $params['pay_type_name'],
		        'company' => $params['company'],
		        'time' => time(),
		        'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
	public function addPlatformMerchantData($params){
		$method = '/pay_api/add_merchant_data';
		$params = array(
		        'merchant_name' => $params['merchant_name'],
		        'merchant_key' => $params['merchant_key'],
		        'merchant_key2' => $params['merchant_key2'],
		        'merchant_key3' => $params['merchant_key3'],
		        'merchant_key4' => $params['merchant_key4'],
		        'pay_type_id' => $params['pay_type_id'],
		        'method_id' => $params['method_id'],
		        'domain_name' => $params['domain_name'],
		        'time' => time(),
		        'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
	public function updatePlatformMerchantData($params){
		$method = '/pay_api/update_merchant_data';
		$params = array(
		        'id' => $params['id'],
		        'merchant_name' => $params['merchant_name'],
		        'merchant_key' => $params['merchant_key'],
		        'merchant_key2' => $params['merchant_key2'],
		        'merchant_key3' => $params['merchant_key3'],
		        'merchant_key4' => $params['merchant_key4'],
		        'pay_type_id' => $params['pay_type_id'],
		        'method_id' => $params['method_id'],
		        'domain_name' => $params['domain_name'],
		        'time' => time(),
		        'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function addPlatformPayMethod($params)
	{
		$method = '/pay_api/add_payment_method';
		$params = array(
			'method_name' => $params['method_name'],
			'method_description' => $params['method_description'],
			'is_recommend' => $params['is_recommend'],
			'method_order' => $params['method_order'],
			'post_url'     => $params['post_url'],
			'html_name'    => $params['html_name'],
			'is_use'       => $params['is_use'],
			'currency'     => $params['currency'],
			'domain_name'  => $params['domain_name'],
			'zone'         => $params['zone'],
			'pay_type_id'  => $params['pay_type_id'],
			'method_id'    => $params['method_id'],
			'class_name'   => $params['class_name'],
			'use_for_month_card' => $params['use_for_month_card'],
			'is_selected'  => $params['is_selected'],
			'client_id' => $this->api_key,
			'time' => time(),
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function updatePlatformPayMethod($params)
	{
		$pay_id = $params['id'];
		$method = '/pay_api/update_payment_method';
		$params['client_id'] = $this->api_key;
		$params['time'] = time();
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		// var_dump($params);die();
		return $this->getResponse($method, $params);
	}

	public function updatePlatformPaymentActivity($params)
	{
		$pay_id = $params['id'];
		$method = '/pay_api/update_payment_activity';
		$params['client_id'] = $this->api_key;
		$params['time'] = time();
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		// var_dump($params);die();
		return $this->getResponse($method, $params);
	}

	public function addPlatformPaymentCurrency($params)
	{
		$method = "/pay_api/add_payment_currency";
		$params = array(
			'pay_type_id' => $params['pay_type_id'],
			'method_id' => $params['method_id'],
			'currency_id' => $params['currency_id'],
			'currency_order' => $params['currency_order'],
			'time' => time(),
		    'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function updatePlatformPaymentCurrency($params)
	{
		$method = "/pay_api/update_payment_currency";
		$params = array(
			'id' => $params['id'],
			'pay_type_id' => $params['pay_type_id'],
			'method_id' => $params['method_id'],
			'currency_id' => $params['currency_id'],
			'currency_order' => $params['currency_order'],
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function addPlatformPayAmount($params){
		$method = "/pay_api/add_payment_amount";
		// $postFields = array(
		// 	'currency_id' => $params['currency_id'],
		// 	'pay_amount' => $params['pay_amount'],
		// 	'yuanbao_amount' => $params['yuanbao_amount'],
		// 	'yuanbao_extra' => $params['yuanbao_extra'],
		// 	'yuanbao_huodong' => $params['yuanbao_huodong'],
		// 	'pay_type_id' => $params['pay_type_id'],
		// 	'method_id' => $params['method_id'],
		// 	'domain_name' => $params['domain_name'],
		// 	'goods_type' => $params['goods_type']
		// 	);
		$params['client_id'] = $this->api_key;
		$params['time'] = time();
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		// var_dump($postFields);die();
		return $this->getResponse($method,$params);
	}

	public function updatePlatformPaymentAmount($params){
		$method = "/pay_api/update_payment_amount";
		$postfileds = array(
			'pay_type_id' => $params['pay_type_id'],
			'method_id' => $params['method_id']
			);
		$postfileds['client_id'] = $this->api_key;
		$postfileds['time'] = time();
		$sign = $this->makeSignMD5($postfileds);
		$postfileds['sign'] = $sign;
		return $this->getResponse($method,$postfileds);
	}

	public function updatePokerCash($params)
	{
		$method = "/home_api/update_award_info";
		$params = array(
			'id' => $params['id'],
			'status' => $params['status'],
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function addPokerOrder($order)
	{
		$method = "/pay_api";
		$params = array(
			'server_id' => $order['server_id'],
			'pay_type_id' => $order['pay_type_id'],
			'method_id' => $order['method_id'],
			'game_id' => $order['game_id'],
			'pay_user_id' => $order['pay_user_id'],
			'currency' => $order['currency'],
			'pay_amount' => $order['pay_amount'],
			'basic_yuanbao_amount' => $order['basic_yuanbao_amount'],
			'extra_yuanbao_amount' => $order['extra_yuanbao_amount'],
			'huodong_yuanbao_amount' => $order['huodong_yuanbao_amount'],
			'yuanbao_amount' => $order['total_yuanbao_amount'],
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function updatesdkRecharge($params)
	{
		$method = "/home_api/update_sdk_recharge";
		$params = array(
			'game_id' => $params['game_id'],
			'sdk_recharge' => $params['sdk_recharge'],
			'time' => time(),
			'client_id' => $this->api_key,
		);
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function joyCardCreate($params)
	{
		$method = "/pay_api/create_joy_card";
		$params['client_id'] = $this->api_key;
		$sign = $this->makeSignMD5($params);
		$params['sign']  = $sign;
		return $this->getResponse($method, $params);
	}

	public function joyCardQuery($params)
	{
		$method = "/pay_api/look_up_joy_card";
		$use_time = json_encode(array('start_time' => $params['start_time'],
						  'end_time' => $params['end_time']));
		$params = array(
			'client_id' => $this->api_key,
			'time' => $params['time'],
			'is_use' => $params['is_use'],
			'owner' => isset($params['owner']) ? $params['owner'] : '',
		);
		if(!$params['owner']){
			unset($params['owner']);
		}
		if($params['is_use'] == 1){	//已经使用的
			$params['use_time'] = $use_time;
		}
		if($params['is_use'] == 2){	//全部
			$params['create_time'] = $use_time;
		}		
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}

	public function joyCardChangeOwner($params)
	{
		$method = "/pay_api/update_joy_card_info";
		$params['client_id'] = $this->api_key;
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		var_export($params);
		return $this->getResponse($method, $params);
	}

	public function modify_game_package($params){
		$method = "/home_api/update_game_package";
		$params['client_id'] = $this->api_key;
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method, $params);
	}
/*
 *google_validate MODIFY
 */
	public function ggvalidateModify($params)
	{
		$method = "/home_api/update_google_validate";
		$params['client_id'] = $this->api_key;
		$sign = $this->makeSignMD5($params);
		$params['sign'] = $sign;
		return $this->getResponse($method,$params);
	}

 //mobile game paytype MODIFY
    public function mobilePaytypeModify($params)
    {
    	if(null == $params['id'])
    	{
    		$method = "/pay_api/add_mobile_payment_method";
    	}
    	else
    	{
    		$method = "/pay_api/update_mobile_payment_method";
    	}
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params);
    }

//third product modify & add
    public function thirdproductModify($params)
    {
    	$method = "/pay_api/update_third_product";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params);
    }   

    public function find_google_order($params){
    	$method = "/home_api/find_google_order";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params);    	
    }
    //手机推送
    public function mobilepush($params){
    	$method = "/home_api/message_push";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function change_device_statu($params){
    	$method = "/home_api/update_limit_type_of_device";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function get_helper_functions($params){
    	$method = "/home_api/get_helper_functions";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function update_helper_function($params){
    	$method = "/home_api/update_helper_function";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function get_helper_function_data($params){
    	$method = "/home_api/get_helper_function_data";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function update_hepler_function_data($params){
    	$method = "/home_api/update_hepler_function_data";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function set_hepler_function($params){
    	$method = "/home_api/set_hepler_function";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function TpApplicationsModify($params){
    	$method = "/home_api/update_tp_applications";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function applicationsModify($params){
    	$method = "/home_api/update_application";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function payapplicationsModify($params){
    	$method = "/pay_api/update_application";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function gamelistQiqiwuModify($params){
    	$method = "/home_api/update_game_list";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function gamelistPaymentModify($params){
    	$method = "/pay_api/update_game_list";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function goodslistModify($params){
    	$method = "/pay_api/update_goods_list";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function modifyTradeseq($params){
    	$method = "/pay_api/update_order_status";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function confirmYuanbao($params){
    	$method = "/pay_api/update_order_status";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function update_reward_lucky($params){
    	$method = "/home_api/update_reward_lucky";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function set_joyspade_activity($params){
    	$method = "/home_api/set_joyspade_activity";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function updateAward($params){
    	$method = "/home_api/update_award_item";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }

    public function updateAwardUser($params){
    	$method = "/home_api/update_award_record";
    	$params['client_id'] = $this->api_key;
    	$sign = $this->makeSignMD5($params);
    	$params['sign'] = $sign;
    	return $this->getResponse($method, $params); 
    }
}
