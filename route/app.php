<?php
// +----------------------------------------------------------------------
// | 猫咪生活报 · REST API 路由定义
// +----------------------------------------------------------------------
use think\facade\Route;
use app\middleware\Auth;

// ─────────────────────────────────────────────
// 无需鉴权的路由（注册 / 登录）
// ─────────────────────────────────────────────
Route::group('api', function () {
    Route::post('auth/register', 'Auth/register');
    Route::post('auth/login', 'Auth/login');
})->middleware(\app\middleware\RateLimit::class);

// ─────────────────────────────────────────────
// 需要鉴权的路由（Bearer Token）
// ─────────────────────────────────────────────
Route::group('api', function () {

    // 退出登录
    Route::post('auth/logout', 'Auth/logout');

    // 仪表盘（首页聚合）
    Route::get('dashboard', 'Dashboard/index');

    // 待办 todo
    Route::group('todo', function () {
        Route::get('', 'Todo/index');
        Route::post('', 'Todo/save');
        Route::put(':id', 'Todo/update');
        Route::delete(':id', 'Todo/delete');
    });

    // 打卡 checkin
    Route::group('checkin', function () {
        Route::get('', 'Checkin/index');
        Route::post('', 'Checkin/save');
        Route::put(':id', 'Checkin/update');
        Route::delete(':id', 'Checkin/delete');
        Route::post(':id/toggle', 'Checkin/toggle');
    });

    // 目标 goal
    Route::group('goal', function () {
        Route::get('', 'Goal/index');
        Route::post('', 'Goal/save');
        Route::put(':id', 'Goal/update');
        Route::delete(':id', 'Goal/delete');
        Route::post(':id/progress', 'Goal/progress');
    });

    // 记账 ledger
    Route::group('ledger', function () {
        Route::get('', 'Ledger/index');
        Route::post('', 'Ledger/save');
        Route::put(':id', 'Ledger/update');
        Route::delete(':id', 'Ledger/delete');
        Route::get('summary', 'Ledger/summary');
    });

    // 笔记 note
    Route::group('note', function () {
        Route::get('', 'Note/index');
        Route::post('', 'Note/save');
        Route::put(':id', 'Note/update');
        Route::delete(':id', 'Note/delete');
    });

})->middleware(Auth::class);
