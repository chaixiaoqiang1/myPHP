<?php
namespace EastBlue\CSV;

class CSV implements CSVInterface {

    private $path = '';

    private $title = array();

    private $handle = '';

    public function init($path, $title)
    {
        $this->path = $path;
        $this->title = $title;
        $this->handle = fopen($this->path, 'w');
        //windows excel BOM头
        fwrite($this->handle, chr(0xEF).chr(0xBB).chr(0xBF));
        if ($this->touchFile())
        {
            fputcsv($this->handle, $title);
            return $this;
        } else
        {
            return false;
        }
    }
    
    // 创建该文件
    private function touchFile()
    {
        $res = touch($this->path);
        return $res;
    }
    // 删除该文件
    public function unlinkFile()
    {
        $res = unlink($this->path);
        return $res;
    }

    public function closeFile()
    {
        if ($this->handle)
        {
            fclose($this->handle);
        }
        return true;
    }

    public function writeData($arr)
    {
        fputcsv($this->handle, (array)$arr);
    }
    // 写入该文件
    private function writeFile($message_arr, $title)
    {
        $handle = fopen($this->path, 'w');
        fputcsv($handle, $title);
        foreach ($message_arr as $row)
        {
            $row = (array) $row;
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}