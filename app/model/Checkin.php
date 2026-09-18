<?php
declare (strict_types = 1);

namespace app\model;

/**
 * 打卡模型
 * 字段: id, user_id, name, emoji, streak, log(JSON: {"YYYY-MM-DD": true})
 */
class Checkin extends BaseModel
{
    protected $name = 'checkins';

    // 不自动写入时间戳（表中无时间字段）
    protected $autoWriteTimestamp = false;

    /**
     * log 读取器 —— JSON 字符串解码为数组
     * @param string|null $value
     * @return array
     */
    public function getLogAttr($value): array
    {
        if (empty($value)) {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * log 修改器 —— 数组编码为 JSON 字符串
     * @param mixed $value
     * @return string
     */
    public function setLogAttr($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return (string) $value;
    }
}
