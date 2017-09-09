<?php

class PlatformInformationController extends \BaseController {
   public function formdata_modify(){
        $table_name = Input::get('table_name');
        $id = Input::get('id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
        $platform = Platform::find(Session::get('platform_id'));

        if($table_name == "tp_applications"){
            $tp_code = Input::get('tp_code');
            $app_id = Input::get('app_id');
            $app_secret = Input::get('app_secret');
            $app_access_token = Input::get('app_access_token');
            $fanpage_url = Input::get('fanpage_url');
            $api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
            $data = array(
                'id' => $id,
                'game_id' =>$game_id,
                'tp_code' => $tp_code,
                'app_id' => $app_id,
                'app_secret' => $app_secret,
                'app_access_token' => $app_access_token,
                'fanpage_url' => $fanpage_url,
                'time' => time(),
            );
            $response = $api->tpApplicationsModify($data);
        }elseif($table_name == "applications"){
            $name = Input::get('name');
            $client_id = Input::get('client_id');
            $client_secret = Input::get('client_secret');
            $redirect_uri = Input::get('redirect_uri');
            $auto_approve = Input::get('auto_approve');
            $autonomous = Input::get('autonomous');
            $status = Input::get('status');
            $suspended = Input::get('suspended');
            $notes = Input::get('notes');
            $api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
            $data = array(
                'game_id' =>$game_id,
                'name' => $name,
                'client_id1' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'auto_approve' => $auto_approve,
                'autonomous' => $autonomous,
                'status' => $status,
                'suspended' => $suspended,
                'notes' => $notes,
                'time' => time()
                );
            $response = $api->applicationsModify($data); 

            $pay_api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
            $response2 = $pay_api->payapplicationsModify($data); 
        }elseif($table_name == "gamelistqiqiwu"){
            $game_name = Input::get('game_name');
            $game_lib = Input::get('game_lib');
            $short_name = Input::get('short_name');
            $url = Input::get('url');
            $api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
            $data = array(
                'game_id' => $game_id,
                'game_name'=>$game_name,
                'game_lib' => $game_lib,
                'short_name' => $short_name,
                'url' => $url,
                'time' => time(),
                );
            $response = $api->gamelistQiqiwuModify($data);
        }elseif($table_name == "gamelistpayment"){
            $game_name = Input::get('game_name');
            $game_lib = Input::get('game_lib');
            $on_recharge = Input::get('on_recharge');
            $sdk_recharge = Input::get('sdk_recharge');
            $giftbag_recharge = Input::get('giftbag_recharge');
            $version = Input::get('version');
            $cs_email = Input::get('cs_email');
            $fb_name = Input::get('fb_name');
            $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
            $data = array(
                'game_id' => $game_id,
                'game_name'=>$game_name,
                'game_lib' => $game_lib,
                'on_recharge' => $on_recharge,
                'sdk_recharge' => $sdk_recharge,
                'giftbag_recharge' => $giftbag_recharge,
                'version' => $version,
                'cs_email' => $cs_email,
                'fb_name' => $fb_name,
                'time' => time(),
                );
            $response = $api->gamelistPaymentModify($data);
        }elseif($table_name == "goodslist"){
            $goods_type_id = Input::get('goods_type_id');
            $goods_name = Input::get('goods_name');
            $on_recharge = Input::get('on_recharge');
           
            $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
            $data = array(
                'game_id' => $game_id,
                'goods_type_id'=>$goods_type_id,
                'goods_name' => $goods_name,
                'on_recharge' => $on_recharge,
                'time' => time(),
                );
            $response = $api->goodslistModify($data);
        }

        if ($response->http_code == 200 && $response->body)
        {   
            if($response->body->error == 0){
                return Response::json($response->body);
            }
            else{
                return Response::json($response->body, 403);
            }
        } else
        {
            return $api->sendResponse();
        }
   }

    public function GamePackageAdIndex(){    //查看每个包的sdk广告信息
        $platform_id = Session::get('platform_id');
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);

        if(2 != $game->game_type){
            return $this->show_message('401', 'Not a mobile game');
        }

        $package_id = Input::get('id');

        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);

        $response = $api->get_game_package($game_id, $platform_id, '');
        $data2view = array();

        if ('200' != $response->http_code){
            $data2view = array();
        }else{
            $response = $response->body;
            foreach ($response as $value) {
                if(isset($value->sdk_ad_info) && $value->sdk_ad_info){
                    $tmp = (array)json_decode($value->sdk_ad_info);
                }else{
                    $tmp = array();
                }
                $tmp['id'] = $value->id;
                $tmp['package_name'] = $value->package_name;
                $tmp['os_type'] = $value->os_type;
                $data2view[] = $tmp;
                unset($tmp);
                unset($value);
            }
        }
        $platform_api_url = Platform::find($platform_id)->platform_api_url;
        $platform_api_url .= '/upload_img?game_id='.$game_id;
        $view = array(
            'content' => View::make('platformapi.qiqiwu_table.sdk_ad_info',
                array(
                    'data' => $data2view,
                    'platform_api_url' => $platform_api_url,
                    )),
            );
        return View::make('main', $view);
    }

    public function GamePackageAdModify(){
        $data = array(
            'id' => (int)Input::get('id'),
            'time' => time(),
            );
        if(!$data['id']){
             return Response::json(array('error_description' => 'No such record.'), 401);
        }
        $sdk_ad_info = array(
            'sdkAdImg' => Input::get('sdkAdImg'),
            'sdkAdUrl' => Input::get('sdkAdUrl'),
            );
        $sdk_ad_info = json_encode($sdk_ad_info);
        $data['sdk_ad_info'] = $sdk_ad_info;
        $platform_id = Session::get('platform_id');
        $platform = Platform::find($platform_id);
        $platform_api = PlatformApi::connect($platform->platform_api_url, $platform->api_key, $platform->api_secret_key);
        $result = $platform_api->modify_game_package($data);

        if('200' == $result->http_code){
            $response = array('msg' => '修改成功');
            return Response::json($response);
        }else{
            return $platform_api->sendResponse();
        }
    }
}