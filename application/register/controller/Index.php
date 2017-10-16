<?php
namespace app\register\controller;
use think\View;
use think\Loader;
use think\Request;
use think\Db;

class Index
{
    public function index()
    {
         // 渲染模板输出
        $view = new View();
        return $view->fetch('register');
    }
    public function registerMesg()
    {
        // 获取post数据
        $request = Request::instance();
        $face_token = $request->post("face_token");         // face_token
        $name = $request->post("name");                     //姓名
        $password = md5( $request->post("password") );      //密码
        $address = md5( $request->post("address") );        //身份证地址
        $birthday = md5( $request->post("birthday") );      //生日
        $gender = md5( $request->post("gender") );          //性别
        $id_card_number = md5( $request->post("id_card_number") );  //身份证号码
        $race = md5( $request->post("race") );              //民族
        $phone_number = md5( $request->post("phone_number") ); //电话号码
        $nick_name = md5( $request->post("nick_name") );     //昵称
              
        if ( empty( $request->post() ) ) {                   //判断是否有有post数据
            echo "没有获取到face_token";
            exit(0);
        }

        // 操作
        Db::query("INSERT INTO `media_user`(`face_token`, `name`, `password`, `address`, `birthday`, `gender`, `id_card_number`, `race`, `phone_number`, `nick_name`) VALUES (?,?,?,?,?,?,?,?,?,?)",[$face_token,$name,$password,$address,$birthday,$gender,$id_card_number,$race,$phone_number,$nick_name]);
        $data = Db::query("SELECT `id`, `name`, `password` FROM `media_user` WHERE `face_token`=?",[$face_token]);

        if (empty($data)) {
            echo "注册失败！";
            exit(0);
        }
        echo 1;
    }
}