# 猫咪生活报 · 后端 API

[![CI](https://github.com/cfhfcwl/CatLifeNews/actions/workflows/ci.yml/badge.svg)](https://github.com/cfhfcwl/CatLifeNews/actions/workflows/ci.yml)

基于 **ThinkPHP 8** 的 REST API 服务，为「猫咪生活报」H5 前端提供数据接口。

业务场景是一个轻量个人生活管理应用，包含五块功能：**待办、打卡、目标、记账、笔记**，配套一个聚合首页仪表盘。所有业务数据按用户隔离，注册登录后凭 Token 访问。

---

## 技术栈

| 项 | 选型 |
|---|---|
| 语言 / 框架 | PHP 8.0+ / ThinkPHP 8 |
| 数据库 | MySQL 5.7+（utf8mb4） |
| 鉴权 | Bearer Token（路由中间件） |
| 密码存储 | `password_hash()` bcrypt |
| 响应格式 | 统一 JSON：`{ code, msg, data }` |
| CI | GitHub Actions（依赖校验 + 全量 PHP 语法检查 + 敏感文件检查） |

**PHP 扩展要求**：`pdo_mysql`、`mbstring`、`json`、`openssl`、`fileinfo`

---

## Docker 启动（推荐）

无需安装 PHP / MySQL / 宝塔：

​```bash
cp .env.docker.example .env   # 按需改两个密码
docker compose up -d --build  # 首次构建约 3–10 分钟
docker compose exec php php think migrate:run
docker compose exec php php think seed:run   # 演示数据（可选）
​```

打开 http://localhost:8080

## 快速启动

### 1. 安装依赖

```bash
composer install
```
### 2. 配置环境变量

```bash
cp .example.env .env
```

然后按本机情况修改 `.env`：

| 变量 | 说明 | 默认值 |
|---|---|---|
| `DB_HOST` | 数据库主机 | 127.0.0.1 |
| `DB_NAME` | 数据库名 | cat_lifenews |
| `DB_USER` | 数据库用户 | — |
| `DB_PASS` | 数据库密码 | — |
| `DB_PORT` | 端口 | 3306 |
| `DB_PREFIX` | 表前缀 | cw_ |
| `APP_DEBUG` | 调试模式 | true |

> `.env` 已被 `.gitignore` 排除，请勿提交。仓库里只保留占位值的 `.example.env`。

### 3. 创建数据库

```bash
cp .example.env .env        # 按本机情况修改数据库配置
composer install
php think migrate:run       # 建表
php think seed:run          # 演示数据（可选
```

示例数据包含 1 个用户、3 条待办、2 个打卡项、2 个目标、5 条记账、2 篇笔记，导入后即可直接调接口看效果。

### 4. 启动开发服务器

```bash
php think run --host 0.0.0.0 --port 8000
```

访问 `http://localhost:8000`。

### 示例账号

| 用户名 | 密码 |
|---|---|
| catlover | 123456 |

---

## 接口清单

所有接口前缀 `/api`，统一响应格式：

```json
{ "code": 0, "msg": "ok", "data": null }
```

`code = 0` 表示成功，非 0 表示失败。

### 公开接口（无需 Token，带限流）

| 方法 | 路径 | 说明 | 请求体 |
|---|---|---|---|
| POST | /api/auth/register | 注册 | `{username, password}` |
| POST | /api/auth/login | 登录 | `{username, password}` |

### 需鉴权接口

请求头：`Authorization: Bearer {token}`

| 方法 | 路径 | 说明 | 请求体 / 参数 |
|---|---|---|---|
| POST | /api/auth/logout | 退出登录 | — |
| GET | /api/dashboard | 首页聚合数据 | — |
| GET | /api/todo | 待办列表 | — |
| POST | /api/todo | 新增待办 | `{title, priority?, done?, note?}` |
| PUT | /api/todo/:id | 更新待办 | `{title?, priority?, done?, note?}` |
| DELETE | /api/todo/:id | 删除待办 | — |
| GET | /api/checkin | 打卡列表 | — |
| POST | /api/checkin | 新增打卡项 | `{name, emoji?}` |
| PUT | /api/checkin/:id | 更新打卡项 | `{name?, emoji?}` |
| DELETE | /api/checkin/:id | 删除打卡项 | — |
| POST | /api/checkin/:id/toggle | 切换今日打卡（自动维护 streak） | — |
| GET | /api/goal | 目标列表 | — |
| POST | /api/goal | 新增目标 | `{name, emoji?, current?, target, unit?, note?}` |
| PUT | /api/goal/:id | 更新目标 | `{name?, emoji?, current?, target?, unit?, note?}` |
| DELETE | /api/goal/:id | 删除目标 | — |
| POST | /api/goal/:id/progress | 目标进度 +1 | — |
| GET | /api/ledger | 记账列表 | — |
| POST | /api/ledger | 新增记账 | `{kind, category, amount, note?, date?}` |
| PUT | /api/ledger/:id | 更新记账 | `{kind?, category?, amount?, note?, date?}` |
| DELETE | /api/ledger/:id | 删除记账 | — |
| GET | /api/ledger/summary | 本月汇总（收入 / 支出 / 分类占比） | — |
| GET | /api/note | 笔记列表 | — |
| POST | /api/note | 新增笔记 | `{title, content?, mood?, date?}` |
| PUT | /api/note/:id | 更新笔记 | `{title?, content?, mood?, date?}` |
| DELETE | /api/note/:id | 删除笔记 | — |

### 返回示例

**登录成功**

```json
{
  "code": 0,
  "msg": "登录成功",
  "data": {
    "token": "a1b2c3d4...",
    "user": { "id": 1, "username": "catlover", "avatar": null, "created_at": "2026-08-14 12:00:00" }
  }
}
```

**首页仪表盘**

```json
{
  "code": 0,
  "msg": "ok",
  "data": {
    "todo":    { "done": 1, "total": 3 },
    "checkin": { "done": 2, "total": 2 },
    "goal":    { "progress": 36 },
    "ledger":  { "income": 8500.00, "expense": 468.40 }
  }
}
```

**记账汇总**

```json
{
  "code": 0,
  "msg": "ok",
  "data": {
    "income": 8500.00,
    "expense": 468.40,
    "balance": 8031.60,
    "categories": [
      { "category": "猫粮", "total": 347.90, "ratio": 74.3 },
      { "category": "餐饮", "total": 120.50, "ratio": 25.7 }
    ]
  }
}
```

---

## 实现要点

四个中间件分层处理横切关注点：

| 中间件 | 职责 |
|---|---|
| `Cors` | 全局跨域，放行 `GET/POST/PUT/DELETE/OPTIONS`，OPTIONS 预检直接返回 200 |
| `RateLimit` | 挂在注册 / 登录路由组上，防止撞库 |
| `Auth` | 解析 `Bearer Token` 并写入 `Request::$user`，未通过统一返回 401 |
| `ForceHttps` | 生产环境强制跳转 HTTPS |

其他设计取舍：

- **路由集中定义**：全部路由写在 `route/app.php`，用 `Route::group` 按业务分组并统一挂中间件，接口权限一目了然
- **统一响应与异常**：`BaseController` 提供 `ok()` / `fail()`，`ExceptionHandle` 把异常转成统一 JSON，控制器里不出现裸 `die` / `echo`
- **数据隔离**：`BaseModel` 自动写入并过滤 `user_id`，所有查询只返回当前登录用户的数据
- **打卡用 JSON 字段存日志**：`checkins.log` 存 `{"2026-08-14": true}`，`toggle` 接口负责维护连续天数 `streak`，避免为打卡记录单独建表
- **金额用 `DECIMAL(10,2)`**：不用 float，避免浮点精度误差

---

## 目录结构

```
.
├── app/
│   ├── BaseController.php        # 基类控制器（ok / fail）
│   ├── ExceptionHandle.php       # 全局异常 → 统一 JSON
│   ├── Request.php               # 请求对象（扩展 $user）
│   ├── AppService.php
│   ├── common.php                # 公共函数
│   ├── provider.php              # 容器绑定
│   ├── middleware.php            # 全局中间件注册（CORS）
│   ├── event.php
│   ├── controller/
│   │   ├── Auth.php              # 注册 / 登录 / 退出
│   │   ├── Dashboard.php         # 首页聚合
│   │   ├── Todo.php
│   │   ├── Checkin.php
│   │   ├── Goal.php
│   │   ├── Ledger.php
│   │   └── Note.php
│   ├── middleware/
│   │   ├── Auth.php
│   │   ├── Cors.php
│   │   ├── ForceHttps.php
│   │   └── RateLimit.php
│   └── model/
│       ├── BaseModel.php         # 自动写 / 过滤 user_id
│       ├── User.php
│       ├── Todo.php
│       ├── Checkin.php
│       ├── Goal.php
│       ├── Ledger.php
│       └── Note.php
├── config/                       # app / database / cache / cookie / log / route / session
├── public/
│   ├── index.php                 # 入口文件
│   ├── router.php                # php think run 用的快速路由
│   └── .htaccess
├── route/
│   └── app.php                   # REST 路由定义
├── sql/
│   └── add_updated_at.sql        # 历史补丁脚本（待并入迁移文件）
├── .github/workflows/ci.yml      # CI
├── .example.env                  # 环境变量示例（占位值）
├── composer.json
└── think                         # 命令行入口
```

---

## 数据模型

所有表统一 `cw_` 前缀，业务表按 `user_id` 隔离。

| 模型 | 表名 | 关键字段 |
|---|---|---|
| User | cw_users | username, password(bcrypt), token |
| Todo | cw_todos | title, priority(P0/P1/P2), done, note |
| Checkin | cw_checkins | name, emoji, streak, log(JSON) |
| Goal | cw_goals | name, emoji, current, target, unit, note |
| Ledger | cw_ledgers | kind(income/expense), category, amount, date |
| Note | cw_notes | title, content, mood, date |

---

## 后续计划

- [ ] 用 `think-migration` 替代手写 SQL，表结构变更可版本化、可回滚
- [ ] 补 PHPUnit 单元测试（优先覆盖认证与记账汇总这类含计算的逻辑）
- [ ] 引入 `think-queue`，把打卡 streak 维护、仪表盘聚合改为异步
- [ ] Token 改为带过期时间的方案，退出登录时作废
- [ ] 用 Docker Compose 编排 nginx + php-fpm + mysql + redis，一键启动替代手工装环境
- [ ] 补接口文档示例（Postman / Apifox 集合）

---

## License

Apache-2.0
