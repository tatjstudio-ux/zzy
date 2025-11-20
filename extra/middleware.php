<?php
// application/extra/middleware.php
return [
    // 应用级别的中间件
    'app'     => [
        \app\middleware\SetGlobalVars::class,
    ],
    // 路由级别的中间件
    'route'   => [],
    // 控制器级别的中间件
    'action'  => [],
];