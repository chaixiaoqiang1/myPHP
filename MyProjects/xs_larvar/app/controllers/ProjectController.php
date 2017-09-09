<?php

class ProjectController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    const API_URL = 'http://eb.qiqiwu.com/slave/api/v1';
    
    const API_KEY= '711cd5cca274b89a09196fdae58d9160';

    const API_SECRET_KEY = 'a2c2e0ce904ba934ce82b8a9030d4f46';
    
//     const API_URL = 'http://eastblue.test/slave/api/v1';
    
//     const API_KEY= '622b2030c82c0bc18a9bdac490b2563f';

//     const API_SECRET_KEY = '6fb27ea121af88c49a322e6c892be0e7';

    public function index()
    {
        $projects = Project::get();
        foreach ($projects as $k => &$v)
        {
            if ($v->last_release_time)
            {
                $v->last_release_time = date('Y-m-d H:i:s', 
                        $v->last_release_time);
            }
        }
        unset($v);
        
        $data = array(
                'content' => View::make('project.index', 
                        array(
                                'projects' => $projects)));
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
                'content' => View::make('project.create'));
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
                'project_name' => 'required',
                'project_owner' => 'required',
                'svn_name' => 'required',
        );
        
        $validator = Validator::make(Input::all(), $rules);
        
        $msg = array(
                'code' => Config::get('errorcode.project_add'),
                'error' => Lang::get('error.project_add'));
        if ($validator->fails())
        {
            return Response::json($msg);
        } else
        {
            $project = new Project();
            $project->project_name = trim(Input::get('project_name'));
            $project->project_owner = trim(Input::get('project_owner'));
            $project->svn_name = trim(Input::get('svn_name'));
            $project->release_shell = trim(Input::get('release_shell'));
            $project->exclude_files = trim(Input::get('exclude_files'));
            $project->current_version = (int) Input::get('current_version');
            $project->last_release_time = time();
            $project->last_release_user = trim(Input::get('project_owner'));
            if ($project->save())
            {
                return Response::json(
                        array(
                                'msg' => Lang::get('basic.create_success')));
            } else
            {
                return Response::json($msg);
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
        $project = Project::find($id);
        if (! $project)
        {
            return $this->show_message('404', 'No such project!');
        }
        $logs = EastBlueLog::where('log_key', 'release')->where('desc', 'like', $id.'|%')->orderBy('created_at', 'desc')->limit(1000)->get();
        $project_logs = array();
        foreach ($logs as $v)
        {
            $desc_array = explode("|", $v->desc, 5);
            if ($desc_array[0] == $id)
            {
                $project_logs[] = (object) array(
                        'project_id' => $desc_array[0],
                        'current_version' => $desc_array[1],
                        'last_release_user' => $desc_array[2],
                        'last_release_record' => $desc_array[4],
                        'last_release_time' => date("Y-m-d H:i:s", $desc_array[3]));
            }
        }
        
        $data = array(
                'content' => View::make('project.show', 
                        array(
                                'project_logs' => (object) $project_logs)));
        return View::make('main', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function edit($id)
    {
        $project = Project::find($id);
        if (! $project)
        {
            App::abort(404);
            exit();
        }
        if ($project->last_release_time)
        {
            $project->last_release_time = date('Y-m-d H:i:s', 
                    $project->last_release_time);
        }
        $data = array(
                'content' => View::make('project.edit', 
                        array(
                                'project' => $project)));
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
        $project = Project::find($id);
        $msg = array(
                'code' => Config::get('errorcode.project_edit'),
                'error' => Lang::get('error.project_edit'));
        if (! $project)
        {
            return Response::json($msg, 404);
        }
        $rules = array(
                'project_name' => 'required',
                'project_owner' => 'required',
                'svn_name' => 'required',
        );
        
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            return Response::json($msg);
        } else
        {
            $project->project_name = trim(Input::get('project_name'));
            $project->project_owner = trim(Input::get('project_owner'));
            $project->svn_name = trim(Input::get('svn_name'));
            $project->release_shell = trim(Input::get('release_shell'));
            $project->exclude_files = trim(Input::get('exclude_files'));
            $project->current_version = (int) Input::get('current_version');
            $project->last_release_user = trim(Input::get('last_release_user'));
            $project->last_release_record = trim(
                    Input::get('last_release_record'));
            $project->last_release_time = trim(Input::get('last_release_time'));
            if ($project->save())
            {
                return Response::json(
                        array(
                                'msg' => Lang::get('basic.edit_success')));
            } else
            {
                return Response::json($msg, 500);
            }
        }
    }

    public function release()
    {
        $release_user = Input::get('release_user');
        $release_record = Input::get('release_record');
        $version = Input::get('roll_back_version');
        $version = $version ? $version : 0;
        $project_id = Input::get('project_id');
        $project = Project::find($project_id);
        $msg = array(
                'code' => Config::get('errorcode.release'),
                'error' => Lang::get('error.project_not_found'));
        if (! $project)
        {
            return Response::json($msg, 404);
        }
        $svn_name = $project->svn_name;
        $exclude_files = $project->exclude_files;
        $api = SlaveApi::connect(self::API_URL, self::API_KEY, 
                self::API_SECRET_KEY);
        $response = $api->releaseProjectScript($project_id, $svn_name, $version, 
                $exclude_files);
        if ($response->http_code == 200)
        {
            //
            $version = $response->body->content;
            $release_time = time();
            $project->current_version = $version;
            $project->last_release_user = $release_user;
            $project->last_release_record = $release_record;
            $project->last_release_time = $release_time;
            $project->save();
            $release_log = new EastBlueLog();
            $release_log->user_id = Auth::user()->user_id;
            $release_log->log_key = 'release';
            $release_log->desc = $project_id . '|' . $version . '|' . $release_user . '|' . $release_time . '|' . $release_record;
            $release_log->save();
            return Response::json(array(
                    'data' => 'OK'));
        } else
        {
            return Response::json($msg, $response->http_code);
        }
    }

    public function check()
    {
        $project_id = (int) Input::get('id');
        $project = Project::find($project_id);
        
        $msg = array(
                'code' => Config::get('errorcode.release'),
                'error' => Lang::get('error.project_not_found'));
        if (! $project)
        {
            return Response::json($msg, 404);
        }
        $api = SlaveApi::connect(self::API_URL, self::API_KEY, 
                self::API_SECRET_KEY);
        $response = $api->checkProjectScript($project_id);
        Log::info(var_export($response,true));
        if ($response->http_code == 200)
        {
            return Response::json($response->body);
        } else
        {
            return Response::json($msg, $response->http_code);
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