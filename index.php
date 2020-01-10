<?php
header('Content-Type: text/html; charset=UTF-8');

define('CORE_OK', true);
require_once("./getinfo.class.php");

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$info = new getinfo();
$info->set_id($id); //设置id
$info->get_all(); //获取信息
$info->set_txt_path("infolist/"); //设置存储路径
$info->save_txt(); //保存信息
echo $info->see_txt(); //查看信息