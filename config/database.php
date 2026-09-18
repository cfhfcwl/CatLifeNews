<?php
// +----------------------------------------------------------------------
// | 数据库设置（MySQL · work 表前缀 cw_）
// | 生产环境请通过 .env 配置，此处为兜底默认值
// +----------------------------------------------------------------------

return [
    // 默认使用的数据库连接配置
    'default'         => env('DB_DRIVER', 'mysql'),

    // 自定义时间查询规则
    'time_query_rule' => [],

    // 自动写入时间戳字段
    // 'datetime' 表示以 Y-m-d H:i:s 格式写入
    // 模型中设置 $autoWriteTimestamp = true 时使用此类型
    'auto_timestamp'  => 'datetime',

    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',

    // 时间字段配置 配置格式：create_time,update_time
    'datetime_field'  => '',

    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'            => env('DB_TYPE', 'mysql'),
            // 服务器地址
            'hostname'        => env('DB_HOST', '127.0.0.1'),
            // 数据库名 —— 共享数据库 work
            'database'        => env('DB_NAME', 'work'),
            // 用户名 —— 数据库用户 root
            'username'        => env('DB_USER', 'root'),
            // 密码
            'password'        => env('DB_PASS', ''),
            // 端口
            'hostport'        => env('DB_PORT', '3306'),
            // 数据库连接参数
            'params'          => [],
            // 数据库编码
            'charset'         => env('DB_CHARSET', 'utf8mb4'),
            // 数据库表前缀 —— 共享数据库用 cw_ 前缀避免冲突
            'prefix'          => env('DB_PREFIX', 'cw_'),

            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'          => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'     => false,
            // 读写分离后 主服务器数量
            'master_num'      => 1,
            // 指定从服务器序号
            'slave_no'        => '',
            // 是否严格检查字段是否存在
            'fields_strict'   => true,
            // 是否需要断线重连
            'break_reconnect' => false,
            // 监听SQL
            'trigger_sql'     => env('APP_DEBUG', false),
            // 开启字段缓存
            'fields_cache'    => false,
        ],

        // 更多的数据库配置信息
    ],
];
