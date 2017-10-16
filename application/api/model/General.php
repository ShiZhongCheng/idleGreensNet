<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class General extends Model
{
    // 是否需要登录
    public function ifNeedLogin()
    {
        if ( !Cookie::has('zhanghao','user_') || !Cookie::has('password','user_') ) {
                // 跳转到登录页面
                header("Location: ".$_SERVER['SCRIPT_NAME']."/api/admin/login");
                exit();
        }

        // 获取登录者信息
        $user_zhanghao = Cookie::get('zhanghao','user_');
        $user_password = Cookie::get('password','user_');
        $userMesg = Db::query("SELECT `oppenid`,`name`,`headImageUrl`, `zhanghao`,`role` FROM `physical_teacher` WHERE `zhanghao`=? and `password`=?",[$user_zhanghao,$user_password]);
        $userMesg = $userMesg[0];  
        return $userMesg;
    }
    // 是否拥有权限
    public function ifHavePower()
    {
        if ( !Cookie::has('zhanghao','user_') || !Cookie::has('password','user_') ) {
                // 跳转到登录页面
                header("Location: ".$_SERVER['SCRIPT_NAME']."/api/admin/login");
                exit();
        }

        // 获取登录者信息
        $user_zhanghao = Cookie::get('zhanghao','user_');
        $user_password = Cookie::get('password','user_');
        $userMesg = Db::query("SELECT `oppenid`,`name`,`headImageUrl`, `zhanghao`,`role` FROM `physical_teacher` WHERE `zhanghao`=? and `password`=?",[$user_zhanghao,$user_password]);
        $userMesg = $userMesg[0];

        switch ($userMesg["role"]) {
            case 1:
                return $userMesg;
                break;
            case 0:
                echo "<script>alert('您当前账号未拥有权限进入此页面!');history.go(-1);</script>";
                break;
            default:
                echo "<script>alert('您当前账号未拥有权限进入此页面!');history.go(-1);</script>";
                break;
        }
    }
    public function getUserMesg($openid)
    {
        $userMesg = Db::query("SELECT * FROM `physical_student` WHERE `oppenid`=?",[$openid]);
        $userMesg = $userMesg[0];
        return $userMesg;
    }
}