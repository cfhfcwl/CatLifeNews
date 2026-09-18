<?php
declare (strict_types = 1);

namespace app\model;

/**
 * 笔记/日记模型
 * 字段: id, user_id, title, content, mood, date, created_at
 */
class Note extends BaseModel
{
    protected $name = 'notes';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'created_at';
    protected $updateTime         = false;
}
