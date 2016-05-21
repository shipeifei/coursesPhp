<?php


define("APP_PATH", dirname(__FILE__));
define("SP_PATH", dirname(__FILE__) . '/SpeedPHP');
$spConfig = array(
    "db" => array(// 数据库设置
        'host' => 'localhost', // 数据库地址，一般都可以是localhost
        'login' => 'root', // 数据库用户名
        'password' => '', // 数据库密码
        'database' => 'course-cms', // 数据库的库名称
    ),
);
require(SP_PATH . "/SpeedPHP.php");


//判断apiKey是否正确
if($_REQUEST['apiKey']!='IloveKEjian')
{
       $arr = array(
            'resultCode' => 502,
            'resultMessage' => 'apiKey无效'
        );
        exit(json_encode($arr));
   
}

//定义全局客户端请求版本以及请求平台
define('API_VERSION', $_REQUEST['version']);
define('API_PLATFORM',$_REQUEST['platform']);
// 这里是入口文件全局位置
import(APP_PATH.'/include/functions.php');
spRun();
