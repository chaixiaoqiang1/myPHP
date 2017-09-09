<?php

class ExchangeRateController extends \BaseController{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
    	$game = Game::find(Session::get('game_id'));
    	$platform = Platform::find(Session::get('platform_id'));
    	$api = SlaveApi::connect($game->eb_api_url, $game->eb_api_key, $game->eb_api_secret_key);
    	$response = $api->getExchangeRate($platform->platform_id);
//     	var_dump($response);die();
		$all_exchange = Rate::orderBy('rate_id','asc')->get();
        if('200' == $response->http_code){
            $body = $response->body;
        }else{
            $body = array();
        }
		$id =1;
		$current_exchanges = array();
		foreach ($body as $item){
			$current_exchange['rate_id'] = $item->id;
			$current_exchange['from'] = $item->type;
			$current_exchange['to'] = 2;
			$current_exchange['multiplier_rate'] = $item->exchange;
			$current_exchange['updated_at'] = $item->timeline;
			$current_exchange['created_at'] = $item->timeline;
			$current_exchange['user'] = $item->admin;
			$current_exchanges[] = (object) $current_exchange;
		}
        $data = array(
			'content' => View::make('payment.rate.index', array(
				'all_exchange' => $all_exchange,
				'current_exchange' => $current_exchanges,
				'platform' => $platform,
				'game' => $game,
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
        $data = array(
            'content' => View::make('payment.rate.create')
        );
        return View::make("main", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $msg = array(
            'code' => Config::get('errorcode.exchange_rate_add'),
            'error' => Lang::get('error.exchange_rate_add')
        );
        $rules = array(
            'from' => 'required',
            'multiplier_rate' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        } else {
            $currency = new Rate();
            $currency->from = (int) Input::get('from');
            $currency->multiplier_rate = trim(Input::get('multiplier_rate'));
            if ($currency->save()) {
                // 同步到服务器
                $platform = Platform::find(Session::get('platform_id'));
                $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
                $response = $api->createExchangeRates($currency->multiplier_rate, $currency->from, Auth::user()->username);
                $body = $response->body;
                if ($response->http_code == 200) {
                    return Response::json(array(
                        'msg' => Lang::get('basic.create_success')
                    ));
                } else {
                    return Response::json($body, $response->http_code);
                }
            } else {
                return Response::json($msg, 500);
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
        $rate = Rate::find($id);
        if (! $rate) {
            App::abort(404);
            exit();
        }
        $data = array(
            'content' => View::make('payment.rate.edit', array(
                'rate' => $rate
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
//     	var_dump($id);
//     	var_dump(Input::all());die();
        $rate = Rate::find($id);
        $msg = array(
            'code' => Config::get('errorcode.exchange_rate_update'),
            'error' => Lang::get('error.exchange_rate_update')
        );
        if (! $rate) {
            return Response::json($msg, 404);
        }
        $rules = array(
            'multiplier_rate' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Response::json($msg, 403);
        } else {
            $rate->multiplier_rate = trim(Input::get('multiplier_rate'));
            if ($rate->save()) {
                // 同步到服务器
                $platform = Platform::find(Session::get('platform_id'));
                $api = PlatformApi::connect($platform->payment_api_url, $platform->api_key, $platform->api_secret_key);
                $response = $api->createExchangeRates($rate->multiplier_rate, $rate->from, Auth::user()->username);
                $body = $response->body;
                if ($response->http_code == 200) {
                    return Response::json(array(
                        'msg' => Lang::get('basic.create_success')
                    ));
                } else {
                    return Response::json($body, $response->http_code);
                }
            } else {
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
}