<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use app\model\Note as NoteModel;

/**
 * 笔记/日记控制器
 * GET/POST /api/note；PUT/DELETE /api/note/:id
 */
class Note extends BaseController
{
    /**
     * 笔记列表
     * GET /api/note
     */
    public function index()
    {
        $userId = $this->request->user->id;
        $list   = NoteModel::where('user_id', $userId)
            ->order('date', 'desc')
            ->order('id', 'desc')
            ->select();

        return $this->ok($list);
    }

    /**
     * 新增笔记
     * POST /api/note
     */
    public function save()
    {
        $userId = $this->request->user->id;
        $data   = $this->request->param();

        $title = trim($data['title'] ?? '');
        if ($title === '') {
            return $this->fail('标题不能为空');
        }

        $date = $data['date'] ?? date('Y-m-d');
        if (!validate_date($date)) {
            return $this->fail('日期格式不正确');
        }

        $note = NoteModel::create([
            'user_id' => $userId,
            'title'   => str_truncate($title, 80),
            'content' => str_truncate($data['content'] ?? '', 2000),
            'mood'    => str_truncate($data['mood'] ?? '', 10),
            'date'    => $date,
        ]);

        return $this->ok($note, '创建成功');
    }

    /**
     * 更新笔记
     * PUT /api/note/:id
     */
    public function update($id)
    {
        $userId = $this->request->user->id;
        $note   = NoteModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$note) {
            return $this->fail('笔记不存在');
        }

        $data = $this->request->param();
        unset($data['id'], $data['user_id']);

        $note->allowField(['title', 'content', 'mood', 'date'])->save($data);

        return $this->ok($note, '更新成功');
    }

    /**
     * 删除笔记
     * DELETE /api/note/:id
     */
    public function delete($id)
    {
        $userId = $this->request->user->id;
        $note   = NoteModel::where('id', $id)->where('user_id', $userId)->find();

        if (!$note) {
            return $this->fail('笔记不存在');
        }

        $note->delete();

        return $this->ok([], '删除成功');
    }
}
