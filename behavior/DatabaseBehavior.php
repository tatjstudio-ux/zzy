<?php
namespace app\behavior;

class DatabaseBehavior
{
    public function appInit(&$params)
    {
        // 获取当前请求的域名
        $host = $_SERVER['HTTP_HOST'];
        // 提取域名前缀
        $prefix = explode('.', $host)[0];

        // 根据域名前缀动态配置数据库连接
        switch ($prefix) {
            case 'zzy':
                $config = [
                    'type'            => 'mysql',
                    'hostname'        => '127.0.0.1',
                    'database'        => 'zzy',
                    'username'        => 'zzy',
                    'password'        => 'tj1234',
                    'hostport'        => '3306',
                    'charset'         => 'utf8',
                    'prefix'          => '',
                ];
                break;
            case 'yd':
                $config = [
                    'type'            => 'mysql',
                    'hostname'        => '127.0.0.1',
                    'database'        => 'yd',
                    'username'        => 'yd',
                    'password'        => 'tj1234',
                    'hostport'        => '3306',
                    'charset'         => 'utf8',
                    'prefix'          => '',
                ];
                break;
            default:
                // 使用默认数据库配置
                $config = include APP_PATH . 'database.php';
                break;
        }

        // 动态设置数据库连接信息
        \think\Db::setConfig($config);
    }
}    