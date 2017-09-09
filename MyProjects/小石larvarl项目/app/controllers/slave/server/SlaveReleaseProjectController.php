<?php

class SlaveReleaseProjectController extends \BaseController {
    
    const SVN_URL = "https://svnsh.digi800.com/svn/src/web/trunk/xinyoudi/web/";
    
    protected $project_id = 0;

    protected $exclude_files = '';

    protected $svn_name = '';

    protected $version = '';
    
    // protected $status = '';
    public function __construct()
    {
        $this->project_id = (int) Input::get('project_id');
        $this->svn_name = Input::get('svn_name');
        $this->version = Input::get('version');
        $this->exclude_files = Input::get('exclude_files');
        if ($this->version == 0 || $this->version == '')
        {
            //默认获取svn服务器上的最新版本号
            $url = self::SVN_URL . $this->svn_name;
            $this->version = exec(
                    "svn log " . $url .
                             " --limit 1 | grep -E '^r[0-9]+' | awk '{print $1}'| awk -F 'r' '{print $2}'", 
                            $output, $retun_var);
        }
    }

    public function release()
    {
        $message = $this->project_id . ',' . $this->svn_name . ',' .
                 $this->version . ',' . $this->exclude_files . "\n";
        $publishing_file = "/tmp/publishing";
        $publish_file = "/tmp/publishes";
        if (touch($publishing_file))
        {
            $handle = fopen($publish_file, 'a');
            if ($handle)
            {
                fwrite($handle, $message);
            }
            unlink($publishing_file);
        }
        $res = array(
                'content' => $this->version);
        return Response::json($res);
    }

    public function check()
    {
        $version_file = '/tmp/versions/' . $this->project_id . '.txt';
        if (! file_exists($version_file))
        {
            $res = array(
                    'content' => '0');
            return Response::json($res);
        }
        $version_file = escapeshellarg($version_file);
        $res = array(
                'content' => `tail -n 1 $version_file`);
        return Response::json($res);
    }
}