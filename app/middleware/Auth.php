<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\User;
use think\Request;
use think\Response;
use think\exception\HttpResponseException;

/**
 * Token 鉴权中间件
 * 读取 Authorization: Bearer {token}，查询 users.token，
 * 将用户对象挂载到 $request->user；失败返回 401 JSON
 */
class Auth
{
    /**
     * Token 有效期（秒）：30 天
     */
    const TOKEN_TTL = 2592000;

    /**
     * @param Request  $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $authorization = $request->header('authorization', '');

        // 解析 Bearer token
        $token = '';
        if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            $token = trim($matches[1]);
        }

        if (empty($token)) {
            $this->unauthorized('未提供有效的认证令牌');
        }

        // 查询用户
        $user = User::where('token', $token)->find();

        if (empty($user)) {
            $this->unauthorized('令牌无效或已过期，请重新登录');
        }

        // 检查 Token 是否过期
        $updatedAt = $user->getAttr('updated_at');
        if ($updatedAt && strtotime($updatedAt) < time() - self::TOKEN_TTL) {
            $this->unauthorized('登录已过期，请重新登录');
        }

        // 将用户挂载到 request
        $request->user = $user;

        return $next($request);
    }

    /**
     * 返回 401 未授权 JSON 并终止请求
     * @param string $msg
     * @return void
     * @throws HttpResponseException
     */
    protected function unauthorized(string $msg = '未授权'): void
    {
        throw new HttpResponseException(json([
            'code' => 401,
            'msg'  => $msg,
            'data' => [],
        ], 401));
    }
}
