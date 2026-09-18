<?php
// 应用公共文件

/**
 * 生成随机 Token（64 位十六进制字符串）
 * @return string
 */
function generate_token(): string
{
    return bin2hex(random_bytes(32));
}

/**
 * 校验日期格式是否为 YYYY-MM-DD
 * @param string $date
 * @return bool
 */
function validate_date(string $date): bool
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return false;
    }
    return checkdate((int) substr($date, 5, 2), (int) substr($date, 8, 2), (int) substr($date, 0, 4));
}

/**
 * 截断字符串到指定长度
 * @param string $str
 * @param int $maxLen
 * @return string
 */
function str_truncate(string $str, int $maxLen): string
{
    return mb_strlen($str) > $maxLen ? mb_substr($str, 0, $maxLen) : $str;
}
