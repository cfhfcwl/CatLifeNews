<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Checkin as CheckinModel;

/**
 * 打卡控制器
 * GET/POST /api/checkin；PUT/DELETE /api/checkin/:id；POST /api/checkin/:id/toggle
 */
class Checkin extends BaseController
{
    /**
     * 打卡列表
     * GET /api/checkin
     */
    public function index()
    {
        $userId = $this->request->user->id;
        $list   = CheckinModel::where('user_id', $userId)
            ->order('id', 'desc')
            ->select();

        return $this->ok($list);
    }

    /**
     * 新增打卡项
     * POST /api/checkin
     */
    public function save()
    {
        $userId = $this->request->user->id;
        $data   = $this->request->param();

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            return $this->fail('名称不能为空');
        }

        $checkin = CheckinModel::create([
            'user_id' => $userId,
            'name'    => str_truncate($name, 50),
            'emoji'   => $data['emoji'] ?? '🐾',
            'streak'  => 0,
            'log'     => [],
        ]);

        return $this->ok($checkin, '创建成功');
    }

    /**
     * 更新打卡项
     * PUT /api/checkin/:id
     */
    public function update($id)
    {
        $userId  = $this->request->user->id;
        $checkin = CheckinModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$checkin) {
            return $this->fail('打卡项不存在');
        }

        $data = $this->request->param();
        unset($data['id'], $data['user_id']);

        $checkin->allowField(['name', 'emoji'])->save($data);

        return $this->ok($checkin, '更新成功');
    }

    /**
     * 删除打卡项
     * DELETE /api/checkin/:id
     */
    public function delete($id)
    {
        $userId  = $this->request->user->id;
        $checkin = CheckinModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$checkin) {
            return $this->fail('打卡项不存在');
        }

        $checkin->delete();

        return $this->ok([], '删除成功');
    }

    /**
     * 切换今日打卡状态，自动维护连续天数 streak
     * POST /api/checkin/:id/toggle
     */
    public function toggle($id)
    {
        $userId  = $this->request->user->id;
        $checkin = CheckinModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$checkin) {
            return $this->fail('打卡项不存在');
        }

        $today = date('Y-m-d');
        $log   = $checkin->log; // 读取器返回数组

        if (isset($log[$today])) {
            // 今日已打卡 → 取消
            unset($log[$today]);
            $checked = false;
        } else {
            // 今日未打卡 → 打卡
            $log[$today] = true;
            $checked = true;
        }

        // 重新计算连续天数
        $checkin->streak = $this->calculateStreak($log);
        $checkin->log    = $log; // 修改器自动 json_encode
        $checkin->save();

        return $this->ok([
            'checkin' => $checkin,
            'checked' => $checked,
        ], $checked ? '打卡成功' : '已取消打卡');
    }

    /**
     * 根据打卡日志计算连续天数
     * 如果今天已打卡，从今天往前数；否则从昨天往前数
     * @param array $log 打卡日志 {"YYYY-MM-DD": true}
     * @return int
     */
    private function calculateStreak(array $log): int
    {
        $streak = 0;
        $date   = date('Y-m-d');

        // 今天没打卡则从昨天开始算
        if (!isset($log[$date])) {
            $date = date('Y-m-d', strtotime('yesterday'));
        }

        while (isset($log[$date])) {
            $streak++;
            $date = date('Y-m-d', strtotime($date . ' -1 day'));
        }

        return $streak;
    }
}
