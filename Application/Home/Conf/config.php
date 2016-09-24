<?php
return array(
    'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(
        '/^goods-(\d+)(.*)$/'        => 'goods/index?id=:1:2',
        '/^category-(\d+)(.*)$/'        => 'category/index?id=:1:2',

    ), // 默认路由规则 针对模块
);