<?php
declare(strict_types=1);

use think\migration\Migrator;

/**
 * 笔记 / 日记表
 */
class CreateNotesTable extends Migrator
{
    public function change(): void
    {
        $this->table('notes', [
                'engine'    => 'InnoDB',
                'collation' => 'utf8mb4_unicode_ci',
                'comment'   => '笔记与日记',
            ])
            ->addColumn('user_id', 'integer', [
                'comment' => '所属用户 ID',
            ])
            ->addColumn('title', 'string', [
                'limit'   => 255,
                'comment' => '标题',
            ])
            ->addColumn('content', 'text', [
                'null'    => true,
                'comment' => '正文',
            ])
            ->addColumn('mood', 'string', [
                'limit'   => 16,
                'null'    => true,
                'default' => null,
                'comment' => '心情 emoji',
            ])
            ->addColumn('date', 'date', [
                'null'    => true,
                'default' => null,
                'comment' => '记录日期',
            ])
            ->addColumn('created_at', 'datetime', [
                'null'    => true,
                'default' => null,
            ])
            ->addIndex(['user_id'], ['name' => 'idx_user'])
            ->create();
    }
}
