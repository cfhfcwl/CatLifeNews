<?php
declare(strict_types=1);

use think\migration\Migrator;

/**
 * 打卡表
 */
class CreateCheckinsTable extends Migrator
{
    public function change(): void
    {
        $this->table('checkins', [
                'engine'    => 'InnoDB',
                'collation' => 'utf8mb4_unicode_ci',
                'comment'   => '打卡习惯',
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => '所属用户 ID',
            ])
            ->addColumn('name', 'string', [
                'limit'   => 100,
                'comment' => '习惯名称',
            ])
            ->addColumn('emoji', 'string', [
                'limit'   => 16,
                'null'    => true,
                'default' => null,
                'comment' => '图标（utf8mb4 下单字符最多 4 字节）',
            ])
            ->addColumn('streak', 'integer', [
                'default' => 0,
                'comment' => '连续打卡天数',
            ])
            ->addColumn('log', 'text', [
                'null'    => true,
                'comment' => '打卡记录 JSON，形如 {"2026-08-14":true}',
            ])
            ->addIndex(['user_id'], ['name' => 'idx_user'])
            ->create();
    }
}
