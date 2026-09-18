<?php
declare(strict_types=1);

use think\migration\Migrator;

/**
 * 用户表
 *
 * 注意：表名写 'users' 不带前缀，前缀由 config/database.php 的 prefix（cw_）自动拼接，
 * 与 app/model/User.php 里的 protected $name = 'users' 保持一致。
 *
 * 原 database.sql 里的 cw_users + sql/add_updated_at.sql 补丁，
 * 在此合并为一张表的完整定义（新库从零建表，不需要两步）。
 */
class CreateUsersTable extends Migrator
{
    public function change(): void
    {
        $this->table('users', [
                'engine'    => 'InnoDB',
                'collation' => 'utf8mb4_unicode_ci',
                'comment'   => '用户表',
            ])
            ->addColumn('username', 'string', [
                'limit'   => 50,
                'comment' => '用户名',
            ])
            ->addColumn('password', 'string', [
                'limit'   => 255,
                'comment' => 'bcrypt 密码哈希',
            ])
            ->addColumn('avatar', 'string', [
                'limit'   => 255,
                'null'    => true,
                'default' => null,
                'comment' => '头像地址',
            ])
            ->addColumn('token', 'string', [
                'limit'   => 64,
                'null'    => true,
                'default' => null,
                'comment' => '登录令牌',
            ])
            ->addColumn('created_at', 'datetime', [
                'null'    => true,
                'default' => null,
            ])
            ->addColumn('updated_at', 'datetime', [
                'null'    => true,
                'default' => null,
                'comment' => 'Token 过期检查用（原 add_updated_at.sql 补丁）',
            ])
            ->addIndex(['username'], [
                'unique' => true,
                'name'   => 'uk_username',
            ])
            ->create();
    }
}
