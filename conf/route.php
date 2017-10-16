<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '/'   => 'index/index/index',                   //首页
    'sell/' => 'index/index/sell',                 //供应大厅
    'seller/:id' => 'index/index/seller',               //供应商个人主页
    'account/' =>  'index/personal/account',         //卖家个人中心-资料与账号
    'supplier/' => 'index/personal/supplier',        //卖家个人中心-我是供应商
    'introduce/' => 'index/personal/introduce',       //卖家个人中心-我的介绍
    'loction/'  => 'index/personal/loction',         //卖家个人中心-我的位置
    'login/'    => 'index/index/login',
    'gongying/:product_id'  => 'index/index/gongying',           //供应商品页面
    'register/'  => 'index/index/register',           //注册
    'search/'    => 'index/index/search' ,
    'dynamic/'   => 'index/index/dynamic'  ,           //动态
    'publishdynamic/' => 'index/personal/publishdynamic', //商家发布动态
    'dynamic_details/:id'  => 'index/index/dynamic_details',  //动态详情
    'more_supplier'        => 'index/index/more_supplier',     //更多供应商
    'substitutes/:choose1'    => 'index/personal/substitutes',  //我是代养人 
    'myadpotion/:choose2'    => 'index/personal/myadpotion',     //我的代养
    'subshow/:substitutes_id' => 'index/index/subshow',          //代养信息展示
    'editsub/:substitutes_id' => 'index/personal/editsub',       //代养信息重新编辑
    'innerMsg/:type'          => 'index/personal/innerMsg',      //信息通知
    'foster/'                 => 'index/index/foster',           //代养大厅
    'apply/'                  => 'index/personal/apply',
    'faceAuth/'               => 'index/personal/faceAuth',      //人脸识别登录授权
];
