<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\User;

/**
 * 认证控制器
 * 注册 / 登录 / 退出
 */
class Auth extends BaseController
{
    /**
     * 用户注册
     * POST /api/auth/register
     * @return \think\Response
     */
    public function register()
    {
        $username = (string) $this->request->param('username', '');
        $password = (string) $this->request->param('password', '');

        if (empty($username) || empty($password)) {
            return $this->fail('用户名和密码不能为空');
        }

        if (mb_strlen($username) > 50) {
            return $this->fail('用户名不能超过50个字符');
        }

        if (mb_strlen($password) < 6) {
            return $this->fail('密码长度不能少于6位');
        }

        // 检查用户名是否已存在
        $exists = User::where('username', $username)->find();
        if ($exists) {
            return $this->fail('用户名已被占用');
        }

        // 生成 token
        $token = generate_token();

        // 创建用户（密码由模型修改器自动 bcrypt 加密）
        $user = User::create([
            'username' => $username,
            'password' => $password,
            'token'    => $token,
        ]);

        return $this->ok([
            'token' => $token,
            'user'  => $user,
        ], '注册成功');
    }

    /**
     * 用户登录
     * POST /api/auth/login
     * @return \think\Response
     */
    public function login()
    {
        $username = (string) $this->request->param('username', '');
        $password = (string) $this->request->param('password', '');

        if (empty($username) || empty($password)) {
            return $this->fail('用户名和密码不能为空');
        }

        $user = User::where('username', $username)->find();

        if (!$user || !password_verify($password, $user->getAttr('password'))) {
            return $this->fail('用户名或密码错误');
        }

        // 生成新 token 并保存
        $token = generate_token();
        $user->token = $token;
        $user->save();

        return $this->ok([
            'token' => $token,
            'user'  => $user,
        ], '登录成功');
    }

    /**
     * 退出登录
     * POST /api/auth/logout
     * @return \think\Response
     */
    public function logout()
    {
        $user = $this->request->user;

        if ($user) {
            $user->token = null;
            $user->save();
        }

        return $this->ok([], '退出成功');
    }
}
