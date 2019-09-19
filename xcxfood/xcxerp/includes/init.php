<?php
// @header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
//E_ALL 显示所有错误  0 隐藏所有错误
error_reporting(E_ALL);
// session_start();

$path = str_replace("\\","/",dirname(dirname(__FILE__)));

$home = "./assets/home";
$admin = "../assets/admin";
$uploads = "../assets/uploads";
$assets = "../assets";
$homeAssets = "./assets";

//常量 APP_PATH 项目根目录
define("APP_PATH",$path);

//前台目录 HOME
define("HOME_PATH",$home);
define("ADMIN_PATH",$admin);
define("UPLOAD_PATH",$uploads);
define("ASSETS_PATH",$assets);
define("HOME_ASSETS",$homeAssets);


//写一个自动加载的函数 PHP内置 自动触发 当发现 new 实例化类不存在的时候会自动调用
function __autoload($classname)
{
  $classname = strtolower($classname);
  include_once("extends/class.$classname.php");
}

$db = new DB("localhost","root","root","xcxfood");

$Strings = new Strings();

$uploads = new Uploads();


include_once("helpers.php");
?>