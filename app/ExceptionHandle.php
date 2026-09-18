<?php
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // HttpResponseException 已包含预构建的 Response，直接返回（保留 HTTP 状态码）
        if ($e instanceof HttpResponseException) {
            $response = $e->getResponse();
            return $response->header([
                'Access-Control-Allow-Origin'      => env('CORS_ORIGIN', 'null'),
                'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, X-Requested-With, Accept, Origin',
                'X-Content-Type-Options'           => 'nosniff',
                'X-Frame-Options'                  => 'DENY',
            ]);
        }

        // API 统一返回 JSON 格式
        $msg  = $e->getMessage();
        $code = $e->getCode();

        // 异常码非正数时使用默认错误码
        if (!is_int($code) || $code <= 0) {
            $code = 1;
        }

        // 非调试模式下隐藏敏感错误信息
        if (!config('app.show_error_msg')) {
            $msg = '服务器内部错误，请稍后再试';
        }

        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => [],
        ])->header([
            'Access-Control-Allow-Origin'      => env('CORS_ORIGIN', 'null'),
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, X-Requested-With, Accept, Origin',
            'X-Content-Type-Options'           => 'nosniff',
            'X-Frame-Options'                  => 'DENY',
        ]);
    }
}
