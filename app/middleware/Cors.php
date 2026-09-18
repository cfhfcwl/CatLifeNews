<?php
declare (strict_types = 1);

namespace app\middleware;

use think\Request;
use think\Response;

/**
 * 跨域中间件
 * 供 H5 前端跨域调用 API
 */
class Cors
{
    /**
     * 处理跨域请求
     * @param Request  $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // 对 OPTIONS 预检请求直接返回 200
        if ($request->isOptions()) {
            return $this->buildCorsResponse(Response::create('', 'html', 200));
        }

        /** @var Response $response */
        $response = $next($request);

        return $this->buildCorsResponse($response);
    }

    /**
     * 给响应附加 CORS 头和安全响应头
     * @param Response $response
     * @return Response
     */
    protected function buildCorsResponse(Response $response): Response
    {
        $origin = $response->getHeader('Origin');
        if (!$origin) {
            $origin = request()->header('origin', '');
        }

        $allowed = env('CORS_ORIGIN', '');
        $allowedOrigins = $allowed ? array_map('trim', explode(',', $allowed)) : [];

        $allowOrigin = '';
        if (in_array($origin, $allowedOrigins)) {
            $allowOrigin = $origin;
        } elseif (empty($allowedOrigins)) {
            $allowOrigin = '';
        }

        return $response->header([
            'Access-Control-Allow-Origin'      => $allowOrigin ?: 'null',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, X-Requested-With, Accept, Origin',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => 86400,
            'X-Content-Type-Options'           => 'nosniff',
            'X-Frame-Options'                  => 'DENY',
            'X-XSS-Protection'                 => '1; mode=block',
            'Referrer-Policy'                  => 'strict-origin-when-cross-origin',
        ]);
    }
}
