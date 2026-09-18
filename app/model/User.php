<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 用户模型
 */
class User extends Model
{
    // 表名（不含前缀，前缀由 database.php 的 prefix 配置自动拼接）
    protected $name = 'users';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'created_at';
    protected $updateTime         = 'updated_at';

    // JSON 序列化时隐藏敏感字段
    protected $hidden = ['password', 'token'];

    /**
     * 密码修改器 —— 自动 bcrypt 加密
     * @param mixed $value 明文密码
     * @return string
     */
    public function setPasswordAttr($value): string
    {
        return password_hash((string) $value, PASSWORD_BCRYPT);
    }
}
