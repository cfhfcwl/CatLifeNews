<?php
// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
    // session name
    'name'           => 'PHPSESSID',
    // SESSION_ID前缀
    'prefix'         => '',
    // 驱动方式 支持file cache
    'type'           => 'file',
    // 是否自动开启
    'auto_start'     => true,
    // 设置cookie有效期
    'expire'         => 1440,
    // session保存路径
    'path'           => '',
    // 是否使用cookie
    'use_cookies'    => true,
    // session缓存前缀
    'cache_prefix'   => 'think_',
    // session 垃圾回收概率
    'gc_probability' => 1,
    // session 垃圾回收最大生命周期
    'gc_maxlifetime' => 1440,
    // 是否安全传输
    'secure'         => false,
    // httponly
    'httponly'       => true,
    // 是否使用 setcookie
    'setcookie'      => true,
    // samesite
    'samesite'       => '',
];
