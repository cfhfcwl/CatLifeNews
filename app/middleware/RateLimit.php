<?php
declare (strict_types = 1);

namespace app\middleware;

use think\Request;
use think\Response;

class RateLimit
{
    public function handle(Request $request, \Closure $next): Response
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        }
        $path = $request->pathinfo();
        $key = md5($ip . ':' . $path);

        $max = 10;
        $ttl = 300;

        $dir = app()->getRuntimePath() . 'rate_limit';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $file = $dir . '/' . $key . '.json';

        $count = 0;
        $now = time();

        if (is_file($file)) {
            $data = json_decode(@file_get_contents($file), true);
            if ($data && ($now - $data['time']) < $ttl) {
                $count = $data['count'];
            }
        }

        if ($count >= $max) {
            return json([
                'code' => 429,
                'msg'  => '请求过于频繁，请稍后再试',
                'data' => [],
            ], 429);
        }

        @file_put_contents($file, json_encode([
            'count' => $count + 1,
            'time'  => $now,
        ]), LOCK_EX);

        return $next($request);
    }
}
