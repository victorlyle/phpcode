<?php
//自动加载类
include_once  __DIR__."/config.php" ;
$include_path = get_include_path() ;
$include_path .= PATH_SEPARATOR.__DIR__."/include/db/" ;
//设置include包含文件所在的所有目录
set_include_path($include_path) ;
/*自动加载类函数*/
spl_autoload_register(function ($class_name) {
    require_once $class_name . ".class.php";
});