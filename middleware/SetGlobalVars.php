<?php
// application/middleware/SetGlobalVars.php
namespace app\middleware;

use think\Request;
use think\View;
use think\Db;

class SetGlobalVars
{
    protected $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function handle(Request $request, \Closure $next)
    {
        // 设置全局变量
        $this->view->assign('appName', 'My Application');


        return $next($request);
    }
}


