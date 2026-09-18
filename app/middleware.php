<?php
// 全局中间件定义文件
return [
    // HTTPS 强制跳转（生产环境）
    \app\middleware\ForceHttps::class,
    // 跨域中间件（供 H5 前端跨域调用）
    \app\middleware\Cors::class,
];
