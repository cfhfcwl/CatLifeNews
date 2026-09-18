<?php
declare (strict_types = 1);

namespace app\model;

/**
 * 目标/进度模型
 * 字段: id, user_id, name, emoji, current, target, unit, note
 */
class Goal extends BaseModel
{
    protected $name = 'goals';

    // 不自动写入时间戳（表中无时间字段）
    protected $autoWriteTimestamp = false;
}
