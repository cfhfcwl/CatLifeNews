<?php
// +----------------------------------------------------------------------
// | 猫咪生活报 · 根目录入口文件
// | 由于宝塔面板运行目录暂未设置为 /public，
// | 此文件作为根级入口，直接引导 ThinkPHP 应用
// | 建议后续在宝塔面板中将运行目录设为 /public 并删除此文件
// +----------------------------------------------------------------------

namespace think;

// 加载 Composer 自动加载
require __DIR__ . '/vendor/autoload.php';

// 执行HTTP应用并响应
$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);
