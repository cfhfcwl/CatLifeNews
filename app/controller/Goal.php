<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Goal as GoalModel;

/**
 * 目标/进度控制器
 * GET/POST /api/goal；PUT/DELETE /api/goal/:id；POST /api/goal/:id/progress
 */
class Goal extends BaseController
{
    /**
     * 目标列表
     * GET /api/goal
     */
    public function index()
    {
        $userId = $this->request->user->id;
        $list   = GoalModel::where('user_id', $userId)
            ->order('id', 'desc')
            ->select();

        return $this->ok($list);
    }

    /**
     * 新增目标
     * POST /api/goal
     */
    public function save()
    {
        $userId = $this->request->user->id;
        $data   = $this->request->param();

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            return $this->fail('目标名称不能为空');
        }

        $target = (int) ($data['target'] ?? 0);
        if ($target <= 0) {
            return $this->fail('目标值必须大于0');
        }

        $goal = GoalModel::create([
            'user_id' => $userId,
            'name'    => str_truncate($name, 50),
            'emoji'   => $data['emoji'] ?? '🎯',
            'current' => (int) ($data['current'] ?? 0),
            'target'  => $target,
            'unit'    => str_truncate($data['unit'] ?? '', 16),
            'note'    => str_truncate($data['note'] ?? '', 255),
        ]);

        return $this->ok($goal, '创建成功');
    }

    /**
     * 更新目标
     * PUT /api/goal/:id
     */
    public function update($id)
    {
        $userId = $this->request->user->id;
        $goal   = GoalModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$goal) {
            return $this->fail('目标不存在');
        }

        $data = $this->request->param();
        unset($data['id'], $data['user_id']);

        $goal->allowField(['name', 'emoji', 'current', 'target', 'unit', 'note'])->save($data);

        return $this->ok($goal, '更新成功');
    }

    /**
     * 删除目标
     * DELETE /api/goal/:id
     */
    public function delete($id)
    {
        $userId = $this->request->user->id;
        $goal   = GoalModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$goal) {
            return $this->fail('目标不存在');
        }

        $goal->delete();

        return $this->ok([], '删除成功');
    }

    /**
     * 目标进度 +1
     * POST /api/goal/:id/progress
     */
    public function progress($id)
    {
        $userId = $this->request->user->id;
        $goal   = GoalModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$goal) {
            return $this->fail('目标不存在');
        }

        $goal->current = $goal->current + 1;
        $goal->save();

        // 计算百分比进度
        $percent = 0;
        if ($goal->target > 0) {
            $percent = min(100, round($goal->current / $goal->target * 100));
        }

        return $this->ok([
            'goal'    => $goal,
            'percent' => (int) $percent,
        ], '进度 +1');
    }
}
