<?php
namespace app\middleware;

use think\Request;
use think\Config;

class DomainDatabaseMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        // 获取当前请求的域名
        $domain = $request->host();
        
        // 根据不同的域名前缀设置不同的数据库配置
        switch ($this->getSubDomain($domain)) {
            case 'dev':
                Config::set('database.database', 'development_db');
                break;
            case 'test':
                Config::set('database.database', 'testing_db');
                break;
            default:
                Config::set('database.database', 'production_db');
                break;
        }

        return $next($request);
    }

    private function getSubDomain($domain)
    {
        // 解析子域名部分
        $subDomains = explode('.', $domain);
        if (count($subDomains) > 2) {
            return $subDomains[0];
        }
        return '';
    }
}

