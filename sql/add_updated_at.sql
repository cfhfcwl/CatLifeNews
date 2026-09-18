-- 为 users 表添加 updated_at 字段，用于 Token 过期检查
ALTER TABLE cw_users ADD COLUMN updated_at DATETIME NULL DEFAULT NULL AFTER created_at;
-- 用 created_at 填充已有记录的 updated_at
UPDATE cw_users SET updated_at = created_at WHERE updated_at IS NULL;
