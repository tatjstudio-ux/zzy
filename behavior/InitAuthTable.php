<?php
namespace app\behavior;
use think\Db;

class InitAuthTable
{
    public function run(&$params)
    {
        // 1. 检测并创建权限表 bm_auth（含 menu_ids 字段）
        $tableExists = Db::query("SHOW TABLES LIKE 'bm_auth'");
        if (empty($tableExists)) {
            $createSql = "CREATE TABLE `bm_auth` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限ID',
                `auth_name` varchar(50) NOT NULL COMMENT '权限名称（唯一）',
                `auth_rule` varchar(200) NOT NULL COMMENT '权限标识（如 admin:all）',
                `menu_ids` varchar(50) NOT NULL DEFAULT '1' COMMENT '可访问菜单序号（逗号分隔，如1,3,6）',
                `auth_desc` text DEFAULT NULL COMMENT '权限描述',
                `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_auth_name` (`auth_name`),
                UNIQUE KEY `idx_auth_rule` (`auth_rule`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限分级表';";
            
            Db::execute($createSql);
            trace('权限表 bm_auth 创建成功（含 menu_ids 字段）', 'info');
        } else {
            // 检测 auth_rule 字段是否存在
            $ruleFieldExists = Db::query("
                SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'bm_auth' 
                AND COLUMN_NAME = 'auth_rule'
            ");
            if (empty($ruleFieldExists)) {
                Db::execute("ALTER TABLE `bm_auth` 
                    ADD COLUMN `auth_rule` varchar(200) NOT NULL COMMENT '权限标识（如 system:user:manage）' AFTER `auth_name`,
                    ADD UNIQUE KEY `idx_auth_rule` (`auth_rule`);");
                trace('权限表 bm_auth 新增 auth_rule 字段', 'info');
            }

            // 检测 menu_ids 字段是否存在
            $menuFieldExists = Db::query("
                SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'bm_auth' 
                AND COLUMN_NAME = 'menu_ids'
            ");
            if (empty($menuFieldExists)) {
                Db::execute("ALTER TABLE `bm_auth` 
                    ADD COLUMN `menu_ids` varchar(50) NOT NULL DEFAULT '1' COMMENT '可访问菜单序号（逗号分隔，如1,3,6）' AFTER `auth_rule`;");
                trace('权限表 bm_auth 新增 menu_ids 字段', 'info');
            }
        }

        // 2. 检测并为用户表 bl_user 添加 auth_id 字段
        $userFieldExists = Db::query("
            SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'bl_user' 
            AND COLUMN_NAME = 'auth_id'
        ");
        
        if (empty($userFieldExists)) {
            $engine = Db::query("SELECT ENGINE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bl_user'");
            if (!empty($engine) && $engine[0]['ENGINE'] != 'InnoDB') {
                Db::execute("ALTER TABLE `bl_user` ENGINE = InnoDB");
                trace('用户表 bl_user 引擎修改为 InnoDB', 'info');
            }

            $alterSql = "ALTER TABLE `bl_user` 
                ADD COLUMN `auth_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '关联权限表 bm_auth 的 ID' AFTER `bm_id`,
                ADD KEY `idx_auth_id` (`auth_id`);";

            Db::execute($alterSql);
            trace('用户表 bl_user 添加 auth_id 字段成功', 'info');
        }

        // 3. 插入默认权限（含菜单权限）
        $defaultAuths = [
            [
                'auth_name' => '管理员',
                'auth_rule' => 'admin:all',
                'menu_ids' => '1,2,3,4,5,6,7,8,9,10,11,12,13',
                'auth_desc' => '系统最高权限，可访问所有模块'
            ],
            [
                'auth_name' => '库管',
                'auth_rule' => 'inventory:manage',
                'menu_ids' => '1,5,6,7',
                'auth_desc' => '负责库存管理、出入库操作'
            ],
            [
                'auth_name' => '销售',
                'auth_rule' => 'sales:manage',
                'menu_ids' => '1,4,7,9',
                'auth_desc' => '负责客户管理、订单销售'
            ],
            [
                'auth_name' => '车间员工',
                'auth_rule' => 'workshop:task',
                'menu_ids' => '1,3',
                'auth_desc' => '负责生产工序、任务执行'
            ],
        ];
        
        foreach ($defaultAuths as $auth) {
            $exists = Db::name('bm_auth')->where('auth_name', $auth['auth_name'])->find();
            if (!$exists) {
                Db::name('bm_auth')->insert([
                    'auth_name' => $auth['auth_name'],
                    'auth_rule' => $auth['auth_rule'],
                    'menu_ids' => $auth['menu_ids'],
                    'auth_desc' => $auth['auth_desc'],
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s'),
                ]);
                trace("插入默认权限：{$auth['auth_name']}", 'info');
            }
        }
    }
}