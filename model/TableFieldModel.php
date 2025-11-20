<?php
namespace app\model;

use think\Model;
use think\Db;

class TableFieldModel extends Model
{
    /**
     * 检测并创建字段
     * @param string $tableName 表名
     * @param string $fieldName 字段名
     * @param string $fieldType 字段类型，默认 VARCHAR(255)
     * @param string $comment 字段注释
     * @param mixed $default 默认值，默认0
     * @return bool
     */
    public function checkAndCreateField($tableName, $fieldName, $fieldType = 'VARCHAR(255)', $comment = '', $default)
    {
        // 检查字段是否存在
        $fields = Db::query("SHOW COLUMNS FROM `{$tableName}` LIKE '{$fieldName}'");

        if (empty($fields)) {
            // 字段不存在，创建它
            $defaultStr = $default !== null ? " DEFAULT '{$default}'" : '';
            $commentStr = $comment ? " COMMENT '{$comment}'" : '';
            try {
                Db::execute("ALTER TABLE `{$tableName}` ADD COLUMN `{$fieldName}` {$fieldType} NULL{$defaultStr}{$commentStr}");
                return true;
            } catch (\Exception $e) {
                // 处理异常
                return false;
            }
        }
        return true;
    }
}     