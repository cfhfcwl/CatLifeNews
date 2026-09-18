<?php
declare(strict_types=1);

use think\migration\Migrator;

/**
 * 记账表
 *
 * 金额刻意使用 DECIMAL(10,2) 而非 FLOAT / DOUBLE ——
 * 浮点数存储金额会产生 0.1 + 0.2 != 0.3 这类精度误差，记账场景不可接受。
 */
class CreateLedgersTable extends Migrator
{
    public function change(): void
    {
        $this->table('ledgers', [
                'engine'    => 'InnoDB',
                'collation' => 'utf8mb4_unicode_ci',
                'comment'   => '收支记账',
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => '所属用户 ID',
            ])
            ->addColumn('kind', 'string', [
                'limit'   => 8,
                'comment' => '类型 income / expense',
            ])
            ->addColumn('category', 'string', [
                'limit'   => 32,
                'comment' => '分类',
            ])
            ->addColumn('amount', 'decimal', [
                'precision' => 10,
                'scale'     => 2,
                'comment'   => '金额，DECIMAL 保证精度',
            ])
            ->addColumn('note', 'string', [
                'limit'   => 255,
                'null'    => true,
                'default' => null,
            ])
            ->addColumn('date', 'date', [
                'comment' => '发生日期',
            ])
            ->addIndex(['user_id'], ['name' => 'idx_user'])
            ->addIndex(['date'], ['name' => 'idx_date'])
            ->create();
    }
}
