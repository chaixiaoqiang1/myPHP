<?php

class PayTypeController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $game = Game::find(Session::get('game_id'));
        $platform = Platform::find(Session::get('platform_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getPlatformPayType($platform->platform_id);
        if ($response->http_code != 200)
        {
            return $this->show_message($response->http_code, json_encode($response->body));
        }
        $body = $response->body;
        $platform_pay_types = array();
        foreach ($body as $item)
        {
            // 更新eastblue数据库pay_types表的platform_type_id字段
//             $eastblue_pay_type = PayType::where('pay_type_id', '=', 
//                     $item->pay_type_id)->where('platform_id', '=', 
//                     $platform->platform_id)->update(
//                     array(
//                             'platform_type_id' => $item->id
//                     ));
            //
            $platform_pay_type = array(
                    "id" => $item->id,
                    "pay_type_id" => $item->pay_type_id,
                    "pay_type_name" => $item->pay_type_name,
                    "company" => $item->company
            );
            $platform_pay_types[] = (object) $platform_pay_type;
        }
        $data = array(
                'content' => View::make('payment.type.index', 
                        array(
                                'platform_pay_types' => $platform_pay_types,
                                'platform' => $platform,
                                'game' => $game
                        ))
        );
        return View::make('main', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $platform_id = Session::get("platform_id");
        $data = array(
                'content' => View::make('payment.type.create', 
                        array(
                                'platform_id' => $platform_id
                        ))
        );
        return View::make('main', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $rules = array(
                'company' => 'required',
                'pay_type_id' => 'required',
                'pay_type_name' => 'required',
                'platform_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        $msg = array(
                'code' => Config::get('errorcode.region_add'),
                'error' => Lang::get('error.region_add')
        );
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        } else
        {
            $type = new PayType();
            $type->company = trim(Input::get('company'));
            $type->pay_type_id = trim(Input::get('pay_type_id'));
            $type->pay_type_name = trim(Input::get('pay_type_name'));
            $type->platform_id = trim(Input::get('platform_id'));
            $is_update_platform = (int)Input::get('is_update_platform');
            if ($type->save()&&$is_update_platform )
            {
                $this->addPlatformPayType($type);
                return Response::json(
                        array(
                                'msg' => Lang::get('type.create_success1')
                        ));
            } else
            {
                return Response::json(
                        array(
                                'msg' => Lang::get('type.create_success2')
                        ));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function edit($id)
    {
        $type = PayType::find($id);
        if (! $type)
        {
            App::abort(404);
            exit();
        }
        $data = array(
                'content' => View::make('payment.type.edit', 
                        array(
                                'type' => $type
                        ))
        );
        return View::make('main', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id            
     * @return Response
     */
    public function update($id)
    {
        $type = PayType::find($id);
        $msg = array(
                'code' => Config::get('errorcode.currency_edit'),
                'error' => Lang::get('error.currency_edit')
        );
        if (! $type)
        {
            return Response::json($msg, 404);
        }
        $rules = array(
                'platform_id' => 'required',
                'company' => 'required',
                'pay_type_name' => 'required',
                'pay_type_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        } else
        {
            $type->platform_id = trim(Input::get('platform_id'));
            $type->company = trim(Input::get('company'));
            $type->pay_type_name = trim(Input::get('pay_type_name'));
            $type->pay_type_id = trim(Input::get('pay_type_id'));
            if ($type->save())
            {
                $this->updatePlatformPayType($type);
                return Response::json(
                        array(
                                'msg' => Lang::get('basic.edit_success')
                        ));
            } else
            {
                return Response::json($msg, 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id            
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    private function addPlatformPayType($type)
    {
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
        $params = array(
                'pay_type_id' => $type->pay_type_id,
                'pay_type_name' => $type->pay_type_name,
                'company' => $type->company
        );
        $response = $api->addPlatformPayType($params);
        if ($response->http_code == 200 && isset($response->body->id))
        {
            $type->platform_type_id = $response->body->id;
            $type->save();
        } else
        {
            App::abort(404);
        }
        return $response;
    }

    private function updatePlatformPayType($type)
    {
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $params = array(
                'id' => $type->platform_type_id,
                'pay_type_id' => $type->pay_type_id,
                'pay_type_name' => $type->pay_type_name,
                'company' => $type->company
                );
        $response = $api->updatePlatformPayType($params);
        if ($response->http_code != 200)
        {
            App::abort(404);
        }
        return $response;
    }
/*
 *Mobile Pay-Type Change
 */
    public function payTypeLoad()                                                  
    {
        $game = Game::find(Session::get('game_id'));   
        $platform = Platform::find(Session::get('platform_id'));
        $platform_id = $platform->platform_id;
        $pay_types = DB::table('mobile_payment_method')->get();
        if(!isset($pay_types))
        {
            App::abort(404);
        }
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.payment',array(
                                                                             'pay_type_info' =>$pay_types,
                                                                             'platform' => $platform,
                                                                             'platform_id' => $platform_id,
                                                                             'game' => $game
                                                                            )
                                        )
                     );
        return View::make('main', $data);

    }
     
    public function payTypeModify()
    {
        $id = Input::get('id');
        $type = DB::table('mobile_game_payment_method')->where('id', $id)->first();
        if(!$id && !isset($type))
        {
            return $this->show_message('404', 'No Such Id');
        }
        $type = (array)$type;
        $data = array(
                'content' => View::make('serverapi.flsg_nszj.payTypeModify', 
                        array(
                                'type' => $type
                        ))
        );
        return View::make('main', $data);
    }

    public function payTypeUpdate()
    {
        $id = Input::get('id');
        $payment_id = Input::get('payment_id');
        $platform_id = Session::get('platform_id');
        if(null == $id)
        {     
            $platform_primary_key = '';
        }
        else
        {
            $platform_primary_key = Input::get('platform_primary_key');
        }
        $data = array( 'id' => $id,
                       'payment_id' => $payment_id,
                       'platform_id' => $platform_id,
                       'pay_lib' => Input::get('pay_lib'),
                       'domain_name' => Input::get('domain_name'),
                       'method_name' => Input::get('method_name'),
                       'pay_type_id' => Input::get('pay_type_id'),
                       'method_id' => Input::get('method_id'),
                       'currency' => Input::get('currency'),
                       'use_type' => Input::get('use_type'),
                       'payment_type' => Input::get('payment_type'),
                       'method_order' => Input::get('method_order'),
                       'img_source' => Input::get('img_source'),
                       'extra' => Input::get('extra'),
                       'tips' => Input::get('tips'),
                       'platform_primary_key' => $platform_primary_key,
                       'special_type' => Input::get('special_type'),
                       'time' => time()
                     );
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
        $result = $api->mobilePaytypeModify($data);
        if(('200' == $result->http_code) && (isset($result->body)) && ('success' == $result->body->res))
        {
            unset($data['time']);
            if(null == $data['id'])
            {
                $data['platform_primary_key'] = $result->body->platform_primary_key;
                $result = DB::table('mobile_game_payment_method')->insert(array($data));
                if(!$result)
                {
                    return Response::json(array('msg' => '官网数据新增成功，但本地数据新增错误！'));
                }
                return Response::json(array('msg' => '新增成功'));
            }
            else
            {
                $result = DB::table('mobile_game_payment_method')->where('id', Input::get('id'))->update($data);
                if(!$result)
                {
                    return Response::json(array('msg' => '官网数据修改成功，但本地数据新增错误！'));                    
                }
                return Response::json(array('msg' => '修改成功'));
            }
        }
        else
        {
            return $api->sendResponse();
        }
    }

    public function paytypeAdd()
    { 
        $payment_id = Input::get('id');
        $result = DB::table('mobile_payment_method')->where('payment_id', $payment_id)->first();
        $datas = array( 'id' => '',
                       'payment_id' => $payment_id,
                       'platform_id' => Session::get('platform_id'),
                       'domain_name' => '',
                       'pay_lib' => $result->pay_lib,
                       'method_name' => $result->method_name,
                       'pay_type_id' => '',
                       'method_id' => '',
                       'currency' => '',
                       'use_type' => '',
                       'payment_type' => '',
                       'method_order' => '',
                       'img_source' => '',
                       'extra' => '',
                       'tips' => '',
                       'platform_primary_key' => '',
                       'special_type' => '',
                     );
         $data = array(
                'content' => View::make('serverapi.flsg_nszj.payTypeAdd', 
                        array(
                                'type' => $datas
                        ))
        );
        return View::make('main', $data);
    }
}
