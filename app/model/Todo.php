<?php
declare (strict_types = 1);

namespace app\model;

/**
 * 待办模型
 * 字段: id, user_id, title, priority(P0/P1/P2), done(0/1), note, created_at
 */
class Todo extends BaseModel
{
    protected $name = 'todos';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'created_at';
    protected $updateTime         = false;

    /**
     * done 读取器 —— 转为布尔值
     */
    public function getDoneAttr($value): bool
    {
        return (bool) $value;
    }

    /**
     * done 修改器 —— 布尔值转 0/1
     */
    public function setDoneAttr($value): int
    {
        return $value ? 1 : 0;
    }
}
