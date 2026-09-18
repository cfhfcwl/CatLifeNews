<?php
declare(strict_types=1);

use think\migration\Migrator;

/**
 * 目标 / 进度表
 */
class CreateGoalsTable extends Migrator
{
    public function change(): void
    {
        $this->table('goals', [
                'engine'    => 'InnoDB',
                'collation' => 'utf8mb4_unicode_ci',
                'comment'   => '目标与进度',
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => '所属用户 ID',
            ])
            ->addColumn('name', 'string', [
                'limit'   => 100,
                'comment' => '目标名称',
            ])
            ->addColumn('emoji', 'string', [
                'limit'   => 16,
                'null'    => true,
                'default' => null,
            ])
            ->addColumn('current', 'integer', [
                'default' => 0,
                'comment' => '当前进度',
            ])
            ->addColumn('target', 'integer', [
                'comment' => '目标值',
            ])
            ->addColumn('unit', 'string', [
                'limit'   => 16,
                'null'    => true,
                'default' => null,
                'comment' => '单位',
            ])
            ->addColumn('note', 'string', [
                'limit'   => 255,
                'null'    => true,
                'default' => null,
            ])
            ->addIndex(['user_id'], ['name' => 'idx_user'])
            ->create();
    }
}
