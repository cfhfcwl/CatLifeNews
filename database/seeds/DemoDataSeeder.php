<?php
declare(strict_types=1);

use think\migration\Seeder;

/**
 * 演示数据
 *
 * 与原 database.sql 里的示例数据一致，方便本地起库后直接联调。
 * 账号：catlover / 123456
 *
 * 注意：种子数据只用于本地开发，不要在生产库跑 seed:run。
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // 用户（密码 123456 的 bcrypt 哈希）
        $this->table('users')->insert([
            [
                'username'   => 'catlover',
                'password'   => '$2y$10$/P5BLo7EEn6SZCbuTht7Qe/EXfhC46wQE0X/7Dc9903ShBxF5uNtu',
                'avatar'     => null,
                'token'      => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ])->save();

        // 上面插入的固定是第一条记录，id = 1
        $uid = 1;

        $this->table('todos')->insert([
            ['user_id' => $uid, 'title' => '完成猫咪生活报后端开发', 'priority' => 'P0', 'done' => 1, 'note' => 'ThinkPHP 8 REST API', 'created_at' => $now],
            ['user_id' => $uid, 'title' => '给小橘买猫粮', 'priority' => 'P1', 'done' => 0, 'note' => '皇家室内猫粮', 'created_at' => $now],
            ['user_id' => $uid, 'title' => '整理书桌', 'priority' => 'P2', 'done' => 0, 'note' => null, 'created_at' => $now],
        ])->save();

        $this->table('checkins')->insert([
            ['user_id' => $uid, 'name' => '喝水', 'emoji' => '💧', 'streak' => 4, 'log' => '{"2026-08-11":true,"2026-08-12":true,"2026-08-13":true,"2026-08-14":true}'],
            ['user_id' => $uid, 'name' => '阅读', 'emoji' => '📚', 'streak' => 2, 'log' => '{"2026-08-13":true,"2026-08-14":true}'],
        ])->save();

        $this->table('goals')->insert([
            ['user_id' => $uid, 'name' => '本月阅读', 'emoji' => '📖', 'current' => 8, 'target' => 30, 'unit' => '页', 'note' => '每天读几页'],
            ['user_id' => $uid, 'name' => '跑步计划', 'emoji' => '🏃', 'current' => 12, 'target' => 50, 'unit' => '公里', 'note' => '每周3次'],
        ])->save();

        $this->table('ledgers')->insert([
            ['user_id' => $uid, 'kind' => 'income', 'category' => '工资', 'amount' => 8000.00, 'note' => '8月工资', 'date' => '2026-08-01'],
            ['user_id' => $uid, 'kind' => 'expense', 'category' => '猫粮', 'amount' => 258.00, 'note' => '皇家室内猫粮', 'date' => '2026-08-05'],
            ['user_id' => $uid, 'kind' => 'expense', 'category' => '餐饮', 'amount' => 120.50, 'note' => '午餐', 'date' => '2026-08-10'],
            ['user_id' => $uid, 'kind' => 'expense', 'category' => '猫粮', 'amount' => 89.90, 'note' => '猫零食大礼包', 'date' => '2026-08-12'],
            ['user_id' => $uid, 'kind' => 'income', 'category' => '兼职', 'amount' => 500.00, 'note' => '写稿报酬', 'date' => '2026-08-13'],
        ])->save();

        $this->table('notes')->insert([
            [
                'user_id'    => $uid,
                'title'      => '小橘今天又拆家了',
                'content'    => '早上起来发现沙发被抓花了，但看它一脸无辜的样子实在生不起气来。买了一块新猫抓板，希望能转移注意力。',
                'mood'       => '😵‍💫',
                'date'       => '2026-08-13',
                'created_at' => $now,
            ],
            [
                'user_id'    => $uid,
                'title'      => '安静的午后',
                'content'    => '泡了一杯茶，小橘趴在腿上打呼噜，窗外的蝉鸣很轻，感觉时间都慢了下来。',
                'mood'       => '😌',
                'date'       => '2026-08-14',
                'created_at' => $now,
            ],
        ])->save();
    }
}
