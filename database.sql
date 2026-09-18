-- +----------------------------------------------------------------------
-- | 猫咪生活报 · 数据库建表 + 示例数据
-- | MySQL 5.7+ / utf8mb4
-- +----------------------------------------------------------------------

-- ─────────────────────────────────────────────
-- 用户表
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS `cw_users`;
CREATE TABLE `cw_users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `avatar`     VARCHAR(255) NULL DEFAULT NULL,
  `token`      VARCHAR(64)  NULL DEFAULT NULL,
  `created_at` DATETIME     NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 待办表
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS `cw_todos`;
CREATE TABLE `cw_todos` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT          NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `priority`   VARCHAR(8)   NOT NULL DEFAULT 'P1',
  `done`       TINYINT      NOT NULL DEFAULT 0,
  `note`       VARCHAR(255) NULL DEFAULT NULL,
  `created_at` DATETIME     NULL DEFAULT NULL,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 打卡表
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS `cw_checkins`;
CREATE TABLE `cw_checkins` (
  `id`      INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT          NOT NULL,
  `name`    VARCHAR(100) NOT NULL,
  `emoji`   VARCHAR(16)  NULL DEFAULT NULL,
  `streak`  INT          NOT NULL DEFAULT 0,
  `log`     TEXT         NULL,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 目标/进度表
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS `cw_goals`;
CREATE TABLE `cw_goals` (
  `id`      INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT          NOT NULL,
  `name`    VARCHAR(100) NOT NULL,
  `emoji`   VARCHAR(16)  NULL DEFAULT NULL,
  `current` INT          NOT NULL DEFAULT 0,
  `target`  INT          NOT NULL,
  `unit`    VARCHAR(16)  NULL DEFAULT NULL,
  `note`    VARCHAR(255) NULL DEFAULT NULL,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 记账表
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS `cw_ledgers`;
CREATE TABLE `cw_ledgers` (
  `id`       INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`  INT           NOT NULL,
  `kind`     VARCHAR(8)    NOT NULL,
  `category` VARCHAR(32)   NOT NULL,
  `amount`   DECIMAL(10,2) NOT NULL,
  `note`     VARCHAR(255)  NULL DEFAULT NULL,
  `date`     DATE          NOT NULL,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 笔记/日记表
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS `cw_notes`;
CREATE TABLE `cw_notes` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT          NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `content`    TEXT         NULL,
  `mood`       VARCHAR(16)  NULL DEFAULT NULL,
  `date`       DATE         NULL DEFAULT NULL,
  `created_at` DATETIME     NULL DEFAULT NULL,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════
-- 示例数据
-- ═══════════════════════════════════════════

-- 示例用户：用户名 catlover，明文密码 123456
-- 密码哈希由 PHP password_hash('123456', PASSWORD_BCRYPT) 生成
INSERT INTO `cw_users` (`username`, `password`, `avatar`, `token`, `created_at`) VALUES
('catlover', '$2y$10$/P5BLo7EEn6SZCbuTht7Qe/EXfhC46wQE0X/7Dc9903ShBxF5uNtu', NULL, NULL, NOW());

-- 待办示例
INSERT INTO `cw_todos` (`user_id`, `title`, `priority`, `done`, `note`, `created_at`) VALUES
(1, '完成猫咪生活报后端开发', 'P0', 1, 'ThinkPHP 8 REST API', NOW()),
(1, '给小橘买猫粮', 'P1', 0, '皇家室内猫粮', NOW()),
(1, '整理书桌', 'P2', 0, NULL, NOW());

-- 打卡示例（log 为 JSON：{"YYYY-MM-DD": true}）
INSERT INTO `cw_checkins` (`user_id`, `name`, `emoji`, `streak`, `log`) VALUES
(1, '喝水', '💧', 4, '{"2026-08-14":true,"2026-08-13":true,"2026-08-12":true,"2026-08-11":true}'),
(1, '阅读', '📚', 2, '{"2026-08-14":true,"2026-08-13":true}');

-- 目标示例
INSERT INTO `cw_goals` (`user_id`, `name`, `emoji`, `current`, `target`, `unit`, `note`) VALUES
(1, '本月阅读', '📖', 8, 30, '页', '每天读几页'),
(1, '跑步计划', '🏃', 12, 50, '公里', '每周3次');

-- 记账示例
INSERT INTO `cw_ledgers` (`user_id`, `kind`, `category`, `amount`, `note`, `date`) VALUES
(1, 'income',  '工资',  8000.00, '8月工资',    '2026-08-01'),
(1, 'expense', '猫粮',  258.00,  '皇家室内猫粮', '2026-08-05'),
(1, 'expense', '餐饮',  120.50,  '午餐',        '2026-08-10'),
(1, 'expense', '猫粮',  89.90,   '猫零食大礼包',  '2026-08-12'),
(1, 'income',  '兼职',  500.00,  '写稿报酬',    '2026-08-13');

-- 笔记示例
INSERT INTO `cw_notes` (`user_id`, `title`, `content`, `mood`, `date`, `created_at`) VALUES
(1, '小橘今天又拆家了', '早上起来发现沙发被抓花了，但看它一脸无辜的样子实在生不起气来。买了一块新猫抓板，希望能转移注意力。', '😵‍💫', '2026-08-13', NOW()),
(1, '安静的午后', '泡了一杯茶，小橘趴在腿上打呼噜，窗外的蝉鸣很轻，感觉时间都慢了下来。', '😌', '2026-08-14', NOW());
