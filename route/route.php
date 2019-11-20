<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
/*
 * 接口类路由
 * */
header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
/**
 * 浏览器第一次在处理复杂请求的时候会先发起OPTIONS请求。路由在处理请求的时候会导致PUT请求失败。
 * 在检测到option请求的时候就停止继续执行
 */
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
  exit;
}

Route::group(['prefix' => 'api/'], function () {
    Route::get('/ceshi','api/index/ceshi');//测试接口

    Route::post('/api','api/index/index');//添加上报信息

    Route::post('/allreport','api/index/AllReporting');//展示个人上报全部信息

    Route::post('/onereport','api/index/OneReporting');//展示个人上报单个信息详情

    Route::post('/send','api/index/Smssend');//短信

    Route::post('/Grap','api/index/GrapImg');//短信

    Route::post('/Login','api/index/login');//用户登录

    Route::post('/NotSend','api/index/NotSend');//用户登录

    Route::post('/Rece','api/index/Receipt');//用户登录



});



