<?php
/****
 * 这是一个集成的小型框架
 * @author Eagle  2017年9月7日 13:28:47
 */

//入口文件
//引入框架类
require './framework/Framework.class.php';
//运行项目
Framework::run();


//================================================================================================
//框架核心类

/**
 * 框架的初始化类
 */
class Framework {
    /**
     * 总的执行方法
     */
    public static function run() {
        //依次调用初始化方法
        self::initRequest();
        self::initPath();
        //调用
        self::loadConfig();
        //初始化错误处理的配置
        self::initErrorHandler();
        //注册自己的自动加载方法
        spl_autoload_register(array('Framework', 'itcast_autoload'));
        self::dispatch();
    }

    private static function initErrorHandler() {
        if('dev' == $GLOBALS['config']['app']['run_mode']) {
            ini_set('error_reporting', E_ALL | E_STRICT);
            ini_set('display_errors', 1);
            ini_set('log_errors', 0);
        } elseif ('pro' == $GLOBALS['config']['app']['run_mode']) {
            ini_set('display_errors', 0);
            ini_set('error_log', APP_DIR . 'error.log');
            ini_set('log_errors', 1);
        }

    }

    /**
     * 初始化请求参数
     */
    private static function initRequest() {
        //将获得的三个参数声明称常量
        //常量没有作用域！
        define('PLATFORM', isset($_GET['p']) ? $_GET['p'] : 'back');
        define('CONTROLLER', isset($_GET['c']) ? $_GET['c'] : 'Admin');
        define('ACTION', isset($_GET['a']) ? $_GET['a'] : 'login');
    }

    /**
     * 初始化路径常量
     */
    private static function initPath() {
        define('DS', DIRECTORY_SEPARATOR);//简化目录分隔符名称长度！
        define('ROOT_DIR', dirname(dirname(__FILE__)) . DS);//根
        define('APP_DIR', ROOT_DIR . 'app' . DS);//应用程序
        define('CONT_DIR', APP_DIR . 'controller' . DS);//控制器
        define('CURR_CONT_DIR', CONT_DIR . PLATFORM . DS);//当前控制器
        define('VIEW_DIR', APP_DIR . 'view' . DS);//视图
        define('CURR_VIEW_DIR', VIEW_DIR . PLATFORM . DS);//当前视图
        define('MODEL_DIR', APP_DIR . 'model' . DS);//模型路径
        define('FRAME_DIR', ROOT_DIR . 'framework' . DS);//框架路径
        define('CONFIG_DIR', APP_DIR . 'config' . DS); //配置文件目录
        define('TOOL_DIR', FRAME_DIR . 'tool' . DS);//工具类目录
        define('UPLOAD_DIR', APP_DIR . 'upload' . DS);//上传文件目录
    }

    /**
     * 自定自动加载方法
     *
     * @param $class_name string 需要的类名
     */
    public static function itcast_autoload($class_name) {
//特例
        $map = array(
            'MySQLDB' => FRAME_DIR . 'MySQLDB.class.php',
            'Model' => FRAME_DIR . 'Model.class.php',
            'Controller' => FRAME_DIR . 'Controller.class.php',
        );//该数组，将所有的有限的特例，类与类名的映射，完成一个列表
//判断当前所需要加载的类是否是特例类
        if( isset($map[$class_name]) ) {
//存在该元素，是特例
//直接载入
            require $map[$class_name];
        }
//规律
        elseif (substr($class_name, -10) == 'Controller') {
//控制器
            require CURR_CONT_DIR . $class_name . '.class.php';
        } elseif (substr($class_name, -5) == 'Model') {
//模型
            require MODEL_DIR . $class_name . '.class.php';
        }
        elseif(substr($class_name, -4) == 'Tool') {
            require TOOL_DIR . $class_name . '.class.php';
        }
    }
    /**
     * 请求分发
     */
    private static function dispatch() {
//实例化控制器类
        $controller_name = CONTROLLER . 'Controller';
        $controller = new $controller_name;
//调用相应的方法
        $action_name = ACTION . 'Action';
        $controller->$action_name();
    }
    /**
     * 载入路径常量
     */
    private static function loadConfig() {
        $GLOBALS['config'] = require CONFIG_DIR . 'app.config.php';
    }
}

//==============================================================================================
//主控制器类

class Controller {

    /**
     * @param $url string 目标url
     * @param $message string 提示信息
     * @param $time int 提示停留的秒数，几秒后跳转
     */
    protected function jump($url, $message='', $time=3) {
        if ($message == '') {
//立即
            header('Location: ' . $url);
        } else {
//提示跳转
//判断是否有用户定义的跳转模板
            if (file_exists(CURR_VIEW_DIR . 'jump.html')) {
//使用用户定义的
                require CURR_VIEW_DIR . 'jump.html';
            } else {
//没有，使用默认的
                echo <<<HTML
<HTML>
 <HEAD>
  <TITLE> 提示：$message </TITLE>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html ;charset=utf-8">
  <META HTTP-EQUIV="Refresh" CONTENT="$time; url=$url">
 </HEAD>
 <BODY>
默认的：$message
 </BODY>
</HTML>
HTML;
            }
        }

        die;//强制停止
    }
}

//==========================================================================================
//主模型类


/**
 * 模型的基础类
 */
class Model {
    protected $db;//保存MySQLDB类的对象
    protected $prefix;//前缀
    protected $fields;//所有的字段


    /**
     * 构造方法
     */
    public function __construct() {
        $this->prefix = $GLOBALS['config']['database']['prefix'];
//连接数据库
        $this->initLink();
//获得当前表的字段信息
        $this->getFields();
    }
    /**
     *
     */
    public function getFields() {
//获得描述desc
        $sql = "desc {$this->table()}";
        $fields_rows = $this->db->fetchAll($sql);
//获得其中的字段部分
        foreach ($fields_rows as $row) {
            $this->fields[] = $row['Field'];
            if($row['Key'] == 'PRI') {
//primary key
                $this->fields['pk'] = $row['Field'];
            }
        }
    }

    /**
     * 自动删除
     *
     * @param $pk_value string 当前需要处理的主键值
     *
     * @return bool
     */
    public function autoDelete($pk_value) {
//拼凑delete 的 SQL语句
//delete from 当前表名 where 主键字段=’主键字段值’
        $sql = "delete from {$this->table()} where `{$this->fields['pk']}`='{$pk_value}'";
        return $this->db->query($sql);
    }

    /**
     * 自动查询一行
     *
     * @param $pk_value string 当前需要处理的主键值
     *
     * @return bool
     */
    public function autoSelectRow($pk_value) {
//拼凑delete 的 SQL语句
//select * from 当前表名 where 主键字段=’主键字段值’
        $sql = "select * from {$this->table()} where `{$this->fields['pk']}`='{$pk_value}'";
        return $this->db->fetchRow($sql);
    }

    /**
     * 自动插入
     *
     * @param $data 字段列表
     */
    public function autoInsert($data) {
//insert into 表名 (字段1,字段2,字段N) values ('值1','值2','值N')
//$data = array(
//'字段1'=>'值1',
//'字段2'=>'值2',
//'字段3'=>'值3',
//);
//拼凑insert表名
        $sql = "insert into {$this->table()} ";

//拼凑字段列表部分
        $fields = array_keys($data);//取得所有键
        $fields = array_map(function($v){return '`'.$v.'`';}, $fields);//使用反引号包裹字段名
        $fields_str = implode(', ', $fields);//使用逗号连接起来即可
        $sql .= '(' . $fields_str . ')';

//拼凑值列表部分
        $values = array_map(function($v) {return "'".$v."'";}, $data);//获得所有的值，将值增加引号包裹
        $values_str = implode(', ', $values);//再使用逗号连接
        $sql .= ' values (' . $values_str . ')';

//执行该insert语句
        return $this->db->query($sql);
    }



    /**
     * 初始化数据库的连接
     */
    protected function initLink() {
        $this->db = MySQLDB::getInstance($GLOBALS['config']['database']);
    }

    /**
     * 拼凑真实表名的方法
     */
    protected function table() {
        return '`' . $this->prefix . $this->table_name . '`';
    }
}



//=============================================================================================
//连接数据库类

/**
 * mysql数据操作类
 */
class MySQLDB {
//属性
//对象的初始化属性
    private $host;
    private $port;
    private $user;
    private $pass;
    private $charset;
    private $dbname;

//运行时生成的属性
    private $link;
    private $last_sql;//最后执行的SQL

    private static $instance;//当前的实例对象

    /**
     * 构造方法
     * @access private
     *
     * @param $params array 对象的选项
     */
    private function __construct($params = array()) {
//初始化 属性
        $this->host = isset($params['host']) ? $params['host'] : '127.0.0.1';
        $this->port = isset($params['port']) ? $params['port'] : '3306';
        $this->user = isset($params['user']) ? $params['user'] : 'root';
        $this->pass = isset($params['pass']) ? $params['pass'] : '';
        $this->charset = isset($params['charset']) ? $params['charset'] : 'utf8';
        $this->dbname = isset($params['dbname']) ? $params['dbname'] : '';

//连接数据库
        $this->connect();
//设置字符集
        $this->setCharset();
//设置默认数据库
        $this->selectDB();
    }
    /**
     * 克隆
     * @access private
     */
    private function __clone() {
    }
    /**
     * 获得单例对象
     */
    public static function getInstance($params) {
        if (! (self::$instance instanceof self) ) {
//实例化时，需要将参数传递到构造方法内
            self::$instance = new self($params);
        }
        return self::$instance;
    }

    /**
     * 连接数据库
     */
    private function connect() {
        if(!$link = mysql_connect("$this->host:$this->port", $this->user, $this->pass)) {//$this->host . ':' . $this->port
            echo '连接失败，请检查mysql服务器，与用户信息';
            die;
        } else {
//连接成功，记录连接资源
            $this->link = $link;
        }
    }

    /**
     * 设置字符集
     */
    private function setCharset() {
        $sql = "set names $this->charset";
        return $this->query($sql);
    }

    /**
     * 设置默认数据库
     */
    private function selectDB() {
//判断是否存在一个数据库名
        if($this->dbname === '') {
            return ;
        }

        $sql = "use `$this->dbname`";
        return $this->query($sql);
    }

    /**
     * 执行SQL的方法,PHPDocumentor
     *
     * @param $sql string 待执行的SQL
     *
     * @return mixed 成功返回 资源 或者 true，失败，返回false
     */
    public function query($sql) {
        $this->last_sql = $sql;
//执行，并返回结果
        if(!$result = mysql_query($sql, $this->link)) {
            echo 'SQL执行失败<br>';
            echo '出错了SQL是：', $sql, '<br>';
            echo '错误代码是：', mysql_errno($this->link), '<br>';
            echo '错误信息是：', mysql_error($this->link), '<br>';
            die;
            return false;//象征性的！
        } else {
            return $result;
        }
    }

    /**
     * @param $sql string 待执行的sql
     * @return array 二维
     */
    public function fetchAll($sql) {
//执行
        if ($result = $this->query($sql)) {
//成功
//遍历所有数据，形成一个二维数组
            $rows = array();//初始化
            while($row = mysql_fetch_assoc($result)) {
                $rows[] = $row;
            }
//释放结果集
            mysql_free_result($result);
            return $rows;
        } else {
//执行失败
            return false;
        }
    }

    /**
     * 执行SQL，获得符合条件的第一条记录
     *
     * @param $sql string 待执行的SQL
     *
     * @return array 一维数组
     */
    public function fetchRow($sql) {
        if ($result = $this->query($sql)) {
            $row = mysql_fetch_assoc($result);
            mysql_free_result($result);
            return $row;
        } else {
            return false;
        }
    }

    /**
     * 利用一个SQL，返回符合条件的第一条记录的第一个字段的值
     *
     * @param $sql string 待执行的SQL
     *
     * @return string 执行结果
     */
    public function fetchColumn($sql) {
        if ($result = $this->query($sql) ) {
            if ($row = mysql_fetch_row($result)) {//row返回的是索引数组，因此0元素，一定是第一列
                mysql_free_result($result);
                return $row[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 在序列化时被调用
     *
     * 用于负责指明哪些属性需要被序列化
     *
     * @return array
     */
    public function __sleep() {
        return array('host', 'port', 'user', 'pass', 'charset', 'dbname');
    }
    /**
     * 在反序列化时调用
     *
     * 用于 对对象的属性进行初始化
     */
    public function __wakeup() {
//连接数据库
        $this->connect();
//设置字符集
        $this->setCharset();
//设置默认数据库
        $this->selectDB();
    }
}

//=============================================================================================
//文件上传类


class UploadTool {

    private $upload_dir;//上传目录
    private $max_size;
    private $allow_types;

    private $error_info;

    public function __construct($dir='', $size=2000000, $types=array()) {
        $this->upload_dir = $dir;
        $this->max_size = $size;
        $this->allow_types = empty($types)?array('image/jpeg', 'image/png'):$types;
    }

    public function __set($p_name, $p_value) {
        if (in_array($p_name, array('upload_dir', 'max_size', 'allow_types'))) {
            $this->$p_name = $p_value;
        }
    }
    public function __get($p_name) {
        if ($p_name  == 'error_info') {
            return $this->$p_name;
        }
    }

    /**
     * 拿到一个上传文件的信息
     * 判断其合理和合法性，移动到指定目标
     *
     * @param $file array 包含了5个上传文件信息的数组
     * @param $prefix string 生成文件的前缀
     *
     * @return 成功，目标文件名；失败：false
     */
    function upload($file, $prefix='upload_') {
//判断是否有错误
        if($file['error'] != 0 ) {
//文件上传错误
            switch ($file['error']) {
                case 1:
                    $this->error_info = '文件太大，超出了php.ini的限制';
                    break;
                case 2:
                    $this->error_info = '文件太大，超出了表单内的MAX_FILE_SIZE的限制';
                    break;
                case 3:
                    $this->error_info = '文件没有上传完';
                    break;
                case 4:
                    $this->error_info = '没有上传文件';
                    break;
                case 6:
                case 7:
                    $this->error_info = '临时文件夹错误';
                    break;

            }
            return false;
        }
//判断类型
        if(!in_array($file['type'], $this->allow_types)) {
            $this->error_info = '类型不对';
            return false;
        }
//判断大小
        if($file['size'] > $this->max_size) {
            $this->error_info = '文件过大';
            return false;
        }

//处于安全性的考虑，判断是否是一个真正的上传文件：
        if( !is_uploaded_file($file['tmp_name'])) {
            $this->error_info = '上传文件可疑';
            return false;
        }

//移动
//通常都会为目标文件重启名字
//原则是：不重名，没有特殊字符，有一定的意义！
        $dst_file = uniqid($prefix) . strrchr($file['name'], '.');
//形成子目录
        $sub_dir = date('YmdH');
//判断是否存在
        if(! is_dir($this->upload_dir . $sub_dir)) {
//不存在则创建
            mkdir ($this->upload_dir . $sub_dir);
        }
        if (move_uploaded_file($file['tmp_name'], $this->upload_dir . $sub_dir . DS . $dst_file)) {
//移动成功，上传完毕！
            return $sub_dir . '/' . $dst_file;
        } else {
//失败
            $this->error_info = '移动失败';
            return false;
        }
    }

}

//===========================================================================================
//session入库的工具类


/**
 * session入库的工具类
 */
class SessionDBTool {
    private $db ;//MySQLDB类的对象

    public function __construct() {
        ini_set('session.save_handler', 'user');
//设置处理器方法
        session_set_save_handler(
            array($this, 'sess_open'),
            array($this, 'sess_close'),
            array($this, 'sess_read'),
            array($this, 'sess_write'),
            array($this, 'sess_destroy'),
            array($this, 'sess_gc')
        );
//开启session
        session_start();
    }

    public function sess_open() {
        $this->db = MySQLDB::getInstance($GLOBALS['config']['database']);
    }

    public function sess_close() {
        return true;
    }

    public function sess_read($sess_id) {
        $sql = "select sess_data from `it_session` where sess_id='$sess_id'";
        return (string) $this->db->fetchColumn($sql);
    }

    public function sess_write($sess_id, $sess_data) {
        $expire = time();
        $sql = "insert into it_session values ('$sess_id', '$sess_data', $expire) on duplicate key update sess_data='$sess_data', expire=$expire";
        return $this->db->query($sql);
    }

    public function sess_destroy($sess_id) {
        $sql = "delete from it_session where sess_id='$sess_id'";
        return $this->db->query($sql);
    }

    public function sess_gc($ttl) {
        $last_time = time()-$ttl;
        $sql = "delete from it_session where expire < $last_time";
        return $this->db->query($sql);
    }
}

//=============================================================================================
//分页类

class PageTool {

    /**
     * 形成翻页的html代码方法
     *
     * @param $page
     * @param $pagesize
     * @param $total
     * @param $url 请求地址 index.php?p=xxx&c=Yyy&a=zzz
     * @param $params array 请求的附加参数例如array('pagesize'=>3)
     *
     * @return string 拼凑好翻页html代码！
     */
    public function show($page, $pagesize, $total, $url, $params=array()) {
//计算需要的信息
        $total_page = ceil($total/$pagesize);
//处理url
//判断$url后是否已经携带了参数
        $url_info = parse_url($url);
        if(isset($url_info['query'])) {
            $url .= '&';
        } else {
//没有携带
            $url .= '?';
        }
//拼凑 额外参数到url上
        foreach($params as $key => $value) {
            $url .= $key.'='.$value.'&';
        }
//增加额外的参数
        $url .= 'page=';

//拼凑翻页信息部分
        $info = <<<HTML
总计 <span id="totalRecords">$total</span> 个记录，
分为 <span id="totalPages">$total_page</span> 页，
当前第 <span id="pageCurrent">$page</span> 页，
每页 <input type="text" value="$pagesize" onblur="window.location.href='$url'+'1'+'&pagesize='+this.value" size="3">
HTML;
// index.php?p=back&c=Goods&a=list&pagesize=2&page=1&pagesize=3

//链接部分
        $prev = $page==1?$total_page:($page-1);
        $next = $page==$total_page?1:($page+1);
        $link = <<<HTML
 <a href="{$url}1">第一页</a>
          <a href="{$url}{$prev}">上一页</a>
          <a href="{$url}{$next}">下一页</a>
          <a href="{$url}{$total_page}">最末页</a>
HTML;
//拼凑页码列表
        $option = <<<HTML
<select onchange="window.location.href='$url'+this.value">
HTML;
//opion们
        for($p=1; $p<=$total_page; ++$p) {
            if($p == $page) {
                $option .= <<<HTML
<option value="$p" selected="selected">$p</option>
HTML;
            } else {
                $option .= <<<HTML
<option value="$p">$p</option>
HTML;
            }
        }
        $option .= '</select>';


        return $info . '<span id="page-link">' . $link . $option . '</span>';
    }
}


//=============================================================================================
//验证码类

class CaptchaTool {

    /**
     * 生成验证码的方法
     */
    public function generate() {
//随机得到背景图片
        $rand_bg_file = TOOL_DIR . 'captcha/captcha_bg' . mt_rand(1, 5) . '.jpg';
//创建画布
        $img = imagecreatefromjpeg($rand_bg_file);

//绘制边框
        $white = imagecolorallocate($img, 0xff, 0xff, 0xff);
//不填充矩形
        imagerectangle($img, 0, 0, 144, 19, $white);

//生成码值，随机的4个只包含大写字母，和数字的字符串！
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';//32 个字符
//随机取4个
        $captcha_str = '';
        for($i=1,$strlen=strlen($chars); $i<=4; ++$i) {
            $rand_key = mt_rand(0, $strlen-1);
            $captcha_str .= $chars[$rand_key];
        }
//保存到session中
        @session_start();
        $_SESSION['captcha_code'] = $captcha_str;

//先确定颜色，白黑随机！
        $black = imagecolorallocate($img, 0, 0, 0);
        if(mt_rand(0, 1) == 1 ) {
            $str_color = $white;
        } else {
            $str_color = $black;
        }

//写字符串
        imagestring($img, 5, 60, 3, $captcha_str, $str_color);

//保存
//告知浏览器，发送的是jpeg格式的图片
        header('Content-Type: image/jpeg; charset=utf-8');
        imagejpeg($img);//输出到浏览器端！

//销毁资源
        imagedestroy($img);
    }

    /**
     * 验证验证码
     *
     * @param $code string 用户输入的验证码
     *
     * @return bool
     */
    public function checkCaptcha($code) {
        @session_start();
        return $code == $_SESSION['captcha_code'];
    }

}


//============================================================================================
//图片类


class ImageTool {

    private $create_funcs = array(
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/gif' => 'imagecreatefromgif'
    );
    private $output_funcs = array(
        'image/jpeg' => 'imagejpeg',
        'image/png' => 'imagepng',
        'image/gif' => 'imagegif'
    );


    /**
     * 生成缩略图，补白
     *
     * @param $src_file
     * @param $max_w;
     * @param $max_h;
     *
     * @return 缩略图的图片地址。失败false！
     */
    public function makeThumb($src_file, $max_w, $max_h) {
//判断原图片是否存在
        if (! file_exists($src_file)) {
            $this->error_info = '源文件不存在';
            return false;
        }

//计算原图的宽高
        $src_info = getimagesize($src_file);
        $src_w = $src_info[0];//原图宽
        $src_h = $src_info[1];//原图高

//在增加一个判断！
//如果原图尺寸小于范围（缩略图尺寸）
        if($src_w < $max_w && $src_h < $max_h) {
//则不用判断，直接用原图的
            $dst_w = $src_w;
            $dst_h = $src_h;
        } else {
//比较 宽之比 与 高之比
            if($src_w/$max_w > $src_h/$max_h) {
//宽应该缩放的多
                $dst_w = $max_w;//缩略图的宽为范围的宽
                $dst_h = $src_h/$src_w * $dst_w;//按照原图的宽高比将求出缩略图高
            } else {
                $dst_h = $max_h;
                $dst_w = $src_w/$src_h * $dst_h;
            }
        }

//创建画布
//先确定创建函数
        $create_func = $this->create_funcs[$src_info['mime']];
        $src_img = $create_func($src_file);//基于已有图片创建
//缩略图的大小一致！
        $dst_img = imagecreatetruecolor($max_w, $max_h);//创建一个新的画布

//为缩略图确定颜色,蓝色
        $blue = imagecolorallocate($dst_img, 0x0, 0x0, 0xff);
        imagefill($dst_img, 0, 0, $blue);//填充

//采样，拷贝，修改大小。注意放置的位置！
        $dst_x=($max_w-$dst_w)/2;
        $dst_y=($max_h-$dst_h)/2;
        imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

//导出
//取得原始文件的路径与名字
        $src_dir = dirname($src_file);
        $src_basename = basename($src_file);
        $thumb_file=substr($src_basename, 0, strrpos($src_basename, '.')) . '_thumb' . strrchr($src_basename, '.');
//获得输出函数
        $output_func = $this->output_funcs[$src_info['mime']];
        $output_func($dst_img, $src_dir . DS . $thumb_file);


//销毁
        imagedestroy($dst_img);
        imagedestroy($src_img);
//返回
        return basename($src_dir) . '/' . $thumb_file;
    }
}
