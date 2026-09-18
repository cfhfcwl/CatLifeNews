<?php
declare (strict_types = 1);

namespace app\model;

/**
 * 记账模型
 * 字段: id, user_id, kind(income/expense), category, amount, note, date
 */
class Ledger extends BaseModel
{
    protected $name = 'ledgers';

    // 不自动写入时间戳（表中无时间字段）
    protected $autoWriteTimestamp = false;

    // 类型转换：amount 转为浮点数
    protected $type = [
        'amount' => 'float',
    ];
}
