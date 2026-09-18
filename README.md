# 猫咪生活报 · 后端 API

ThinkPHP 8 单应用 REST API 项目，为「猫咪生活报」H5 前端提供数据接口。

## 环境要求

- PHP >= 8.0
- Composer
- MySQL 5.7+
- PHP 扩展：pdo_mysql、json、openssl、mbstring、fileinfo

## 快速启动

### 1. 安装依赖

```bash
cd server
composer install
```

### 2. 导入数据库

```bash
mysql -u root -p < database.sql
```

此操作会创建 `cat_workbench` 数据库并建表，同时插入示例用户和示例数据。

### 3. 配置数据库连接

复制环境变量示例文件并按需修改：

```bash
cp .example.env .env
```

或直接编辑 `config/database.php` 中的默认值。默认配置为：

| 参数     | 默认值          |
| -------- | --------------- |
| 主机     | 127.0.0.1       |
| 数据库名 | cat_workbench   |
| 用户名   | root            |
| 密码     | root            |
| 端口     | 3306            |
| 编码     | utf8mb4         |

### 4. 启动开发服务器

```bash
php think run --host 0.0.0.0 --port 8000
```

服务启动后访问 `http://localhost:8000`。

## 示例账号

| 用户名   | 密码   |
| -------- | ------ |
| catlover | 123456 |

> 密码使用 PHP `password_hash()` 的 bcrypt 加密存储。
> 如需重新生成哈希：`php -r "echo password_hash('123456', PASSWORD_BCRYPT);"`

## API 接口清单

所有接口前缀 `/api`，统一响应格式：

```json
{ "code": 0, "msg": "ok", "data": ... }
```

> code = 0 表示成功，非 0 表示失败。

### 认证（无需 Token）

| 方法   | 路径                | 说明           | 请求体                          |
| ------ | ------------------- | -------------- | ------------------------------- |
| POST   | /api/auth/register  | 注册           | {username, password}           |
| POST   | /api/auth/login     | 登录           | {username, password}            |

### 需鉴权接口（请求头 `Authorization: Bearer {token}`）

| 方法   | 路径                     | 说明                     | 请求体/参数                                      |
| ------ | ------------------------ | ------------------------ | ------------------------------------------------ |
| POST   | /api/auth/logout         | 退出登录                 | -                                                |
| GET    | /api/dashboard           | 首页聚合数据             | -                                                |
| GET    | /api/todo                | 待办列表                 | -                                                |
| POST   | /api/todo                | 新增待办                 | {title, priority?, done?, note?}                 |
| PUT    | /api/todo/:id            | 更新待办                 | {title?, priority?, done?, note?}                |
| DELETE | /api/todo/:id            | 删除待办                 | -                                                |
| GET    | /api/checkin             | 打卡列表                 | -                                                |
| POST   | /api/checkin             | 新增打卡项               | {name, emoji?}                                   |
| PUT    | /api/checkin/:id         | 更新打卡项               | {name?, emoji?}                                  |
| DELETE | /api/checkin/:id         | 删除打卡项               | -                                                |
| POST   | /api/checkin/:id/toggle  | 切换今日打卡（自动维护 streak） | -                                          |
| GET    | /api/goal                | 目标列表                 | -                                                |
| POST   | /api/goal                | 新增目标                 | {name, emoji?, current?, target, unit?, note?}   |
| PUT    | /api/goal/:id            | 更新目标                 | {name?, emoji?, current?, target?, unit?, note?} |
| DELETE | /api/goal/:id            | 删除目标                 | -                                                |
| POST   | /api/goal/:id/progress   | 目标进度 +1              | -                                                |
| GET    | /api/ledger              | 记账列表                 | -                                                |
| POST   | /api/ledger              | 新增记账                 | {kind, category, amount, note?, date?}           |
| PUT    | /api/ledger/:id          | 更新记账                 | {kind?, category?, amount?, note?, date?}        |
| DELETE | /api/ledger/:id          | 删除记账                 | -                                                |
| GET    | /api/ledger/summary      | 本月汇总（收入/支出/分类占比） | -                                          |
| GET    | /api/note                | 笔记列表                 | -                                                |
| POST   | /api/note                | 新增笔记                 | {title, content?, mood?, date?}                  |
| PUT    | /api/note/:id            | 更新笔记                 | {title?, content?, mood?, date?}                 |
| DELETE | /api/note/:id            | 删除笔记                 | -                                                |

### 接口返回示例

**登录成功：**
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

**仪表盘：**
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

**记账汇总：**
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

## 跨域（CORS）

项目通过全局中间件 `app\middleware\Cors` 处理跨域：

- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With, Accept, Origin`
- OPTIONS 预检请求直接返回 200

H5 前端可直接跨域调用，无需额外配置。

## 项目结构

```
server/
├── app/
│   ├── BaseController.php          # 基类控制器（含 ok/fail）
│   ├── ExceptionHandle.php        # 异常处理（统一 JSON）
│   ├── Request.php                # 请求对象（含 $user 属性）
│   ├── AppService.php             # 应用服务
│   ├── common.php                 # 公共函数
│   ├── provider.php              # 容器绑定
│   ├── middleware.php             # 全局中间件（CORS）
│   ├── event.php                  # 事件定义
│   ├── controller/
│   │   ├── Auth.php               # 认证（注册/登录/退出）
│   │   ├── Dashboard.php          # 仪表盘聚合
│   │   ├── Todo.php              # 待办 CRUD
│   │   ├── Checkin.php           # 打卡 CRUD + toggle
│   │   ├── Goal.php             # 目标 CRUD + progress
│   │   ├── Ledger.php           # 记账 CRUD + summary
│   │   └── Note.php             # 笔记 CRUD
│   ├── middleware/
│   │   ├── Auth.php              # Token 鉴权
│   │   └── Cors.php              # 跨域处理
│   └── model/
│       ├── BaseModel.php         # 基类模型（自动写 user_id）
│       ├── User.php              # 用户
│       ├── Todo.php              # 待办
│       ├── Checkin.php           # 打卡（log JSON 存取）
│       ├── Goal.php              # 目标
│       ├── Ledger.php            # 记账
│       └── Note.php              # 笔记
├── config/
│   ├── app.php                   # 应用配置
│   ├── database.php              # 数据库配置
│   └── route.php                 # 路由配置
├── public/
│   ├── index.php                 # 入口文件
│   ├── router.php                # 快速测试
│   └── .htaccess                 # Apache 重写
├── route/
│   └── app.php                   # REST 路由定义
├── composer.json
├── database.sql                  # 建表 + 示例数据
├── think                         # 命令行入口
├── .example.env                  # 环境变量示例
└── README.md
```

## 数据模型

| 模型     | 表名      | 关键字段                                         |
| -------- | --------- | ------------------------------------------------ |
| User     | users     | username, password(bcrypt), token                |
| Todo     | todos     | title, priority(P0/P1/P2), done, note             |
| Checkin  | checkins  | name, emoji, streak, log(JSON: {"日期": true})   |
| Goal     | goals     | name, emoji, current, target, unit, note          |
| Ledger   | ledgers   | kind(income/expense), category, amount, date      |
| Note     | notes     | title, content, mood, date                         |

所有业务数据按 `user_id` 过滤，仅返回当前登录用户的数据。
