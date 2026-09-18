<?php
declare(strict_types=1);

use think\migration\Migrator;

/**
 * 待办表
 */
class CreateTodosTable extends Migrator
{
    public function change(): void
    {
        $this->table('todos', [
                'engine'    => 'InnoDB',
                'collation' => 'utf8mb4_unicode_ci',
                'comment'   => '待办事项',
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => '所属用户 ID',
            ])
            ->addColumn('title', 'string', [
                'limit'   => 255,
                'comment' => '待办标题',
            ])
            ->addColumn('priority', 'string', [
                'limit'   => 8,
                'default' => 'P1',
                'comment' => '优先级 P0 / P1 / P2',
            ])
            ->addColumn('done', 'boolean', [
                'default' => 0,
                'comment' => '是否完成',
            ])
            ->addColumn('note', 'string', [
                'limit'   => 255,
                'null'    => true,
                'default' => null,
                'comment' => '备注',
            ])
            ->addColumn('created_at', 'datetime', [
                'null'    => true,
                'default' => null,
            ])
            ->addIndex(['user_id'], ['name' => 'idx_user'])
            ->create();
    }
}
