# Web 项目新人攻略

## 入职

安装 HR 的邮件走完入职流程

## 申请 SVN 权限

### Email 格式

	申请 xx 项目 svn 读写权限		

	地址：https://svnsh.i9i8.com/svn/src/web/trunk/xinyoudi/web/xx
	权限：读写
	批准人：叮当
	
**邮件发送给 西索、叮当、ychen@i9i8.com**

## 安装开发环境

* 下载安装自己喜欢的编辑器，Zend 可以到【**\\192.168.0.126\文件中转站\流川枫**】下面下载。
* 服务器环境，请安装 virturalbox，使用 ubuntu12.04服务器版本，软件在 【**\\192.168.0.126\Share\软件汇总\程序上网机必装**】Ubuntu64.rar、VirtualBox-4.3.8-92456-Win.exe
* vbox 使用桥接模式
* 使用 virtrualbox 共享文件夹功能，工作父目录 D:\workspace，在 workspace 下建立 eastblue等项目
* 设置 host
* 192.168.0.xx eastblue.local
* 192.168.0.xx pm.eastblue.local


## EastBlue项目

### 概述

EastBlue为公司内部运营平台项目，使用开源软件搭建：Laravel + AngularJS + BootStrap + MySQL + Redis。目标是建立固定开发流程，方便后续开发扩展，方便新人接手项目。

PS：需要常备上面框架文档进行查阅学习。

### 配置

Nginx服务器Rewrite规则:
	
	try_files $uri $uri/ /index.php?$query_string;

Apache 使用现成文件，public/.htaccess 

建立开发、测试、正式三个配置，在start.php文件中定义local、production、testing
	
	bootstrap/start.php
	
	$env = $app->detectEnvironment(array(                                              
    'local' => array('*.local'),                                                   
    // 'production' => array(),                                                    
    // 'testing' => array()                                                        
	)); 
	 
在config文件夹中新建local、production、testing三个文件夹，每个文件夹下面

	app/local/app.php
	app/local/cache.php
	app/local/database.php
	app/local/session.php
	
Session与Cache都使用Redis进行存储

### 基本代码结构

app/controller/ 下面放具体业务逻辑代码，新建立controller后需要使用下面命令实现自动加载。
	
	php artisan controller:make TestController //创建一个controller
	php artisan dump-autoload //自动加载

app/views/ 下面放置模板  
app/model/ 下面放置数据库对象  

public/js 放置js文件  
public/css 放置css文件  
public/img 放置图片文件


### 数据库

数据库：eastblue
设计程序，数据库结构先行，需要考虑清楚关系结构建好表，最后才进行开发，具体关系可以使用MySQLWorkbench建立ER图。


### 权限管理

1. 系统中的每个功能都当成一个app存储在apps表中，访问每个功能时Request::path()获得的字符串当成app_key存储在app_key字段中。
2. 用户建立的时候分admin与非admin两种，非admin需要在users表permissions字段存储app_id，使用逗号分隔每个app_id
3. 在Laravel中，使用Route::filter功能建立permission Filter，在进入每个App时候都会根据URL进行判断是否有权限进入。
	
	Route::filter('permission', function() {

### 模板渲染

模板还是使用PHP原生的代码，不使用Blade。

所有的功能模板都使用统一的模板main.php加功能模块自身组成
	
		$data = array(
			'content' => View::make('apps.index')
		);
		return View::make('main', $data);

公用的部分在 app/start/global.php 中渲染，在渲染main.php完成函数内的事情。

    View::composer('main', function($view) {
		$groups = Group::all();


### 路由

使用Laravel强大的路由功能，能够非常方便的构造自己需要的访问路径。尽量往REST设计上靠，参阅Laravel的controller的帮助

	Route::resource('users', 'UserController');

### 403、404页面

同样是在app/start/global.php中统一建立403、404方法：

	App::missing(function($exception) {
		$data = array(
			'app_name' => '404 Error Page',
			'app_desc' => '404...',
			'content' => View::make('error404')	
		);

		return Response::view('main', $data, 404);
	});

	App::error(function(Exception $exception, $code)
	{
		Log::error($exception);

如果不是ajax调用，出现错误或未找到可以直接使用App::abort(404)实现。

### 创建一个新模块流程

确定创建的模块功能，例如Region，有简单的create\updte\动作，可以浏览。首先设计好数据库，创建controller，创建Model

	php artisan controller:make RegionCroller //创建controller
	
	app/model/Region.php
	
	//创建view
	app/views/regions/create.php
	app/views/regions/index.php
	app/views/regions/edit.php
	
	//因为使用angularjs，在eastblue.js中新建好对应的表单controller
	
	public/js/eastblue.js
	
	function createRegionController($scope, $http, alertService) {
	
	//在app/routes.php添加route
	Route::resource('regions', RegionController);
	
	
完成上述基本上完成一个模块的开发

在系统添加功能中添加三条数据，区域浏览、区域修改、区域创建

regions
regions/create
regions/*/edit	

在组列表功能中，在对应的组下面选择上面三个功能。




## Laravel PHP Framework

[![Latest Stable Version](https://poser.pugx.org/laravel/framework/version.png)](https://packagist.org/packages/laravel/framework) [![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.png)](https://packagist.org/packages/laravel/framework) [![Build Status](https://travis-ci.org/laravel/framework.png)](https://travis-ci.org/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, and caching.

Laravel aims to make the development process a pleasing one for the developer without sacrificing application functionality. Happy developers make the best code. To this end, we've attempted to combine the very best of what we have seen in other web frameworks, including frameworks implemented in other languages, such as Ruby on Rails, ASP.NET MVC, and Sinatra.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the entire framework can be found on the [Laravel website](http://laravel.com/docs).

### Contributing To Laravel

**All issues and pull requests should be filed on the [laravel/framework](http://github.com/laravel/framework) repository.**

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)




## 关于安装好nginx以及unix server后需要进行的具体配置 ##
1.创建nginx/sites-enabled/eastblue_loacl  以下文件内的配置条目，需要替换为自己本地的共享文件夹下的目录

	server {
        listen 80;
        charset utf-8;
        server_name eastblue.local;

        root /mnt/hgfs/Program/eastblueTest/public;	//配自己的项目位置
        index index.php index.html index.htm;

        access_log /mnt/hgfs/Program/log/eastblueTest_access.log combined;	//配自己的项目位置
        error_log /mnt/hgfs/Program/log/eastblueTest_error.log ;	//配自己的项目位置

        location ~ \.(jpg|jpeg|bmp|png|gif|ico|css|js)$ {
                expires 365d;
        }
        try_files $uri $uri/ /index.php?$query_string;


        location ~ \.php$ {
                include fastcgi_params;
                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                #fastcgi_split_path_info ^((?U).+\.php)(/?.+)$;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                #fastcgi_param PATH_INFO $fastcgi_path_info;
                #fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        }
	}

	#不要忘记重启nginx服务
	#命令为    service nginx restart



2.更新虚拟机以及本地的hosts文件
本地hosts可以将eastblue.local指向虚拟机的Ip
虚拟机上的配置可以参考已有的配置

3.为php5安装mcrypt扩展。
  可能需要执行的命令为:

  	执行:
   	sudo apt-get update
	sudo apt-get install nginx php5-fpm php5-cli php5-mcrypt git

	执行:
	sudo nano /etc/php5/fpm/php.ini
	并找到cgi.fix_pathinfo并修改其值
	cgi.fix_pathinfo=0

	执行:
	sudo php5enmod mcrypt
	sudo service php5-fpm restart

	此时mcrypt扩展应已安装完成，还有问题可参考 https://www.digitalocean.com/community/tutorials/how-to-install-laravel-with-an-nginx-web-server-on-ubuntu-14-04

4.在phpmyadmin中新建数据库，并导入eastblue的本地数据。

5.打开eastblue文件夹下的bootstrap文件夹中的start.php文件
  在第27-31行的内容为：
		$env = $app->detectEnvironment(array(
			'local' => array('*.local', 'localhost.*', '*.dev'),
			// 'production' => array(),
			'test' => array('*.test')
		));

	会根据虚拟机的hostname选择配置文件，*为通配符。

	 修改hostname在虚拟机下命令行中输入
	 hostname
	 即可查看目前的hostname
	 输入
	 hostname testname
	 即可把hostname修改为testname

6.假如我们的项目hostname修改为localhost.local
  那么配置文件即选择了app/config/local下的配置
  数据库等配置在此目录下。

7.配置完成应该就可以在浏览器访问eastblue.local了
