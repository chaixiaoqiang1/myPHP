<?php

class MerchantDataController extends \BaseController {

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
        $response = $api->getPlatformMerchantData($platform->platform_id);
        if ($response->http_code != 200)
        {
            App::abort(404);
        }
        $platform_merchant_data = $response->body;
        $data = array(
                'content' => View::make('payment.merchant.index', 
                        array(
                                'platform_merchant_data' => $platform_merchant_data,
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
                'content' => View::make('payment.merchant.create', 
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
                'merchant_name' => 'required',
                'pay_type_id' => 'required',
                'method_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        $msg = array(
                'code' => Config::get('errorcode.merchant_add'),
                'error' => Lang::get('error.merchant_add')
        );
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        } else
        {
            $intert_id = $this->addPlatformMerchantData(Input::all());
            return Response::json(
                    array(
                            'msg' => $intert_id .
                                     Lang::get('basic.create_success')
                    ));
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
        $game = Game::find(Session::get('game_id'));
        $platform = Platform::find(Session::get('platform_id'));
        $api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, 
                $game->eb_api_secret_key);
        $response = $api->getPlatformMerchantData($platform->platform_id, $id);
        if ($response->http_code != 200)
        {
            App::abort(404);
        }
        $edit_merchant_data = $response->body[0];
        $data = array(
                'content' => View::make('payment.merchant.edit', 
                        array(
                                'edit_merchant_data' => $edit_merchant_data,
                                'platform_id' => $platform->platform_id
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
        $msg = array(
                'code' => Config::get('errorcode.currency_edit'),
                'error' => Lang::get('error.currency_edit')
        );
        $rules = array(
                'merchant_name' => 'required',
                'pay_type_id' => 'required',
                'method_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg, 403);
        } else
        {
            $this->updatePlatformMerchantData(Input::all());
            return Response::json(
                    array(
                            'msg' => Lang::get('basic.edit_success')
                    ));
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

    private function addPlatformMerchantData($params)
    {
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $params = array(
                'merchant_name' => $params['merchant_name'],
                'merchant_key' => $params['merchant_key'],
                'merchant_key2' => $params['merchant_key2'],
                'merchant_key3' => isset($params['merchant_key3']) ? $params['merchant_key3'] : '',
                'merchant_key4' => isset($params['merchant_key4']) ? $params['merchant_key4'] : '',
                'pay_type_id' => $params['pay_type_id'],
                'method_id' => $params['method_id'],
                'domain_name' => $params['domain_name']
        );
        $response = $api->addPlatformMerchantData($params);
        if ($response->http_code == 200)
        {
            $intert_id = $response->body->id;
        } else
        {
            App::abort(404);
        }
        return $intert_id;
    }

    private function updatePlatformMerchantData($params)
    {
        $platform = Platform::find(Session::get('platform_id'));
        $api = PlatformApi::connect($platform->payment_api_url, 
                $platform->api_key, $platform->api_secret_key);
        $params = array(
                'id' => $params['id'],
                'merchant_name' => $params['merchant_name'],
                'merchant_key' => $params['merchant_key'],
                'merchant_key2' => $params['merchant_key2'],
                'merchant_key3' => $params['merchant_key3'],
                'merchant_key4' => isset($params['merchant_key4']) ? $params['merchant_key4'] : '',
                'pay_type_id' => $params['pay_type_id'],
                'method_id' => $params['method_id'],
                'domain_name' => $params['domain_name']
        );
        $response = $api->updatePlatformMerchantData($params);
        if ($response->http_code != 200)
        {
            App::abort(404);
        }
        
        return $response;
    }
}