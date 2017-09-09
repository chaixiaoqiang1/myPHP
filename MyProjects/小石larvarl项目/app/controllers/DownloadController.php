<?php

class DownloadController extends \BaseController{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $now = Input::get('now');
    	$file = storage_path() . "/cache/" . $now . ".csv";
    	$data = array(
    	        'content' => View::make('download',
    	                array(
    	                        'file' => $file)));
    	return View::make('main', $data);
    }
}