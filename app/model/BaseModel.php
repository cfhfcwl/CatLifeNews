<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 业务模型基类
 * 所有带 user_id 的模型继承此类，插入时自动写入当前登录用户 ID
 */
abstract class BaseModel extends Model
{
    /**
     * 插入前自动写入 user_id
     * @param Model $model
     * @return void
     */
    public static function onBeforeInsert(Model $model): void
    {
        $user = request()->user;
        if ($user) {
            $model->setAttr('user_id', $user->id);
        }
    }
}
