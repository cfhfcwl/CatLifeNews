<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Todo as TodoModel;

/**
 * 待办控制器
 * GET/POST /api/todo；PUT/DELETE /api/todo/:id
 */
class Todo extends BaseController
{
    /**
     * 待办列表（按当前用户过滤）
     * GET /api/todo
     */
    public function index()
    {
        $userId = $this->request->user->id;
        $list   = TodoModel::where('user_id', $userId)
            ->order('id', 'desc')
            ->select();

        return $this->ok($list);
    }

    /**
     * 新增待办
     * POST /api/todo
     */
    public function save()
    {
        $userId = $this->request->user->id;
        $data   = $this->request->param();

        $title = trim($data['title'] ?? '');
        if ($title === '') {
            return $this->fail('标题不能为空');
        }

        $priority = $data['priority'] ?? 'P1';
        if (!in_array($priority, ['P0', 'P1', 'P2'])) {
            $priority = 'P1';
        }

        $todo = TodoModel::create([
            'user_id'  => $userId,
            'title'    => str_truncate($title, 120),
            'priority' => $priority,
            'done'     => $data['done'] ?? false,
            'note'     => str_truncate($data['note'] ?? '', 255),
        ]);

        return $this->ok($todo, '创建成功');
    }

    /**
     * 更新待办
     * PUT /api/todo/:id
     */
    public function update($id)
    {
        $userId = $this->request->user->id;
        $todo   = TodoModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$todo) {
            return $this->fail('待办不存在');
        }

        $data = $this->request->param();
        unset($data['id'], $data['user_id']);

        $todo->allowField(['title', 'priority', 'done', 'note'])->save($data);

        return $this->ok($todo, '更新成功');
    }

    /**
     * 删除待办
     * DELETE /api/todo/:id
     */
    public function delete($id)
    {
        $userId = $this->request->user->id;
        $todo   = TodoModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$todo) {
            return $this->fail('待办不存在');
        }

        $todo->delete();

        return $this->ok([], '删除成功');
    }
}
