<?php

class CurrencyController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = array(
			'content' => View::make('payment.currency.index')
		);
		return View::make('main',$data);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$data = array(
            'content' => View::make('payment.currency.create')
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
		    'code'  => Config::get('errorcode.currency_add'),
		    'error' => Lang::get('error.currency_add')
		);		
		$rules = array(
			'currency_code' => 'required',
			'currency_name' => 'required'
		);
		$validator = Validator::make(Input::all(),$rules);
		if($validator->fails()){
			return Response::json($msg,403);
		} else {
			$currency = new Currency;
			$currency->currency_code 	= trim(Input::get('currency_code'));
			$currency->currency_symbol	= trim(Input::get('currency_symbol'));
			$currency->currency_name 	= trim(Input::get('currency_name'));

			if($currency->save()){
				return Response::json(array('msg' => Lang::get('basic.create_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$currency = Currency::find($id);
		if (!$currency) {
			App::abort(404);
			exit;
		}
		$data = array(
			'content' => View::make('payment.currency.edit', array('currency' => $currency))
		);
		return View::make('main', $data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$currency = Currency::find($id);
		$msg = array(
			'code' => Config::get('errorcode.currency_edit'),
			'error'=> Lang::get('error.currency_edit')
		);
		if (!$currency) {
			return Response::json($msg, 404);
		}
		$rules = array(
			'currency_code' => 'required',
			'currency_name' => 'required'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			return Response::json($msg, 403);
		} else {
			$currency->currency_code = trim(Input::get('currency_code'));
			$currency->currency_symbol = trim(Input::get('currency_symbol'));
			$currency->currency_name = trim(Input::get('currency_name'));
			if ($currency->save()) {
				return Response::json(array('msg' => Lang::get('basic.create_success')));
			} else {
				return Response::json($msg, 500);
			}
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}