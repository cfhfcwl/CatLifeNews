<?php
declare (strict_types = 1);

namespace app\middleware;

use think\Request;
use think\Response;

/**
 * HTTPS 强制跳转中间件
 * 生产环境强制使用 HTTPS，开发环境跳过
 */
class ForceHttps
{
    public function handle(Request $request, \Closure $next): Response
    {
        if (!env('FORCE_HTTPS', false)) {
            return $next($request);
        }

        if (!$request->isSsl()) {
            $httpsUrl = 'https://' . $request->host() . $request->url();
            return redirect($httpsUrl, 301);
        }

        $response = $next($request);
        $response->header(['Strict-Transport-Security' => 'max-age=31536000; includeSubDomains']);
        return $response;
    }
}
