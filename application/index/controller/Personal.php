<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Route;
use think\Db;

class Personal
{
    public function account ()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch('index/login');
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $user_mesg = Db::query("SELECT * FROM `media_user` WHERE `id`=?",[$user_id]);
        $head_image_url = $user_mesg[0]["head_image_url"];    //头像
        $nick_name  = $user_mesg[0]["nick_name"];             //昵称
        $phone_number = $user_mesg[0]["phone_number"];        //手机号
        $name = $user_mesg[0]["name"];                        //姓名
        $id_card_number = $user_mesg[0]["id_card_number"];    //身份证号码
        $birthday = $user_mesg[0]["birthday"];                //生日
        $address = $user_mesg[0]["address"];                  //地址

        // 获取当前完整url
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        $backurl = $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
        
        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal-body',[
            'title'           => '卖家个人中心',
            'head_image_url'  => $head_image_url,
            'nick_name'       => $nick_name,
            'phone_number'    =>$phone_number,
            'name'            =>$name,
            'id_card_number'  =>$id_card_number,
            'birthday'        =>$birthday,
            'address'         =>$address,
            'userMesg'        =>$userMesg,
            'backurl'        => $backurl,
            ]);
    }
    public function apply()
    {
        // 渲染模板输出
        $view = new View();
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_apply-body',[
            'title'           => '申请成为供应商',
            'userMesg'        => $userMesg,
            'user_id'         => $user_id,
            ]);
    }
    public function supplier()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch("index/login");
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $user_mesg = Db::query("SELECT `head_image_url`,`nick_name`,`supplier` FROM `media_user` WHERE `id`=?",[$user_id]);
        $supplier = $user_mesg[0]["supplier"];                //供应商状态位
        $head_image_url = $user_mesg[0]["head_image_url"];    //头像
        $nick_name  = $user_mesg[0]["nick_name"];             //昵称

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        // 如果还未成为供应商
        if( $supplier == 0) {
            $view->engine->layout('layout/personal-layout');
            return $view->fetch('index/personal_apply-body',[
                'title'           => '申请成为供应商',
                'head_image_url'  => $head_image_url,
                'nick_name'       => $nick_name,
                'userMesg'        => $userMesg,
                ]);
        }
        // 获取分类
        $classify = Db::query("SELECT `classify_id`, `classify_name` FROM `media_classify` WHERE 1");
        // 获取我的发布记录
        $media_provide = Db::query("SELECT * FROM `media_provide` WHERE `id`=?",[$user_id]);

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_supplier-body',[
            'title'           => '卖家个人中心',
            'head_image_url'  => $head_image_url,
            'nick_name'       => $nick_name,
            'classify'        => $classify,
            'media_provide'   => $media_provide,
            'userMesg'        => $userMesg,
            ]);
    }
    public function introduce()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
           return $view->fetch("index/login");
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $user_mesg = Db::query("SELECT `head_image_url`,`nick_name`,`supplier` FROM `media_user` WHERE `id`=?",[$user_id]);
        $supplier = $user_mesg[0]["supplier"];                //供应商状态位
        $head_image_url = $user_mesg[0]["head_image_url"];    //头像
        $nick_name  = $user_mesg[0]["nick_name"];             //昵称

        // 如果还未成为供应商
        if ($supplier == 0) {
            return redirect("https://www.goooooo.top/index/personal/apply");
        }

        // 获取我的介绍
        $business_introduce = Db::query("SELECT `business_introduce` FROM `media_supplier` WHERE `id`=?",[$user_id]);
        $business_introduce = $business_introduce[0]["business_introduce"];

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_introduce-body',[
            'title'           => '卖家个人中心',
            'head_image_url'  => $head_image_url,
            'nick_name'       => $nick_name,
            'business_introduce' => $business_introduce,
            'userMesg'        => $userMesg,
            ]);
    }
    public function loction()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch("index/login");
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $user_mesg = Db::query("SELECT `head_image_url`,`nick_name`,`supplier` FROM `media_user` WHERE `id`=?",[$user_id]);
        $supplier = $user_mesg[0]["supplier"];                //供应商状态位
        $head_image_url = $user_mesg[0]["head_image_url"];    //头像
        $nick_name  = $user_mesg[0]["nick_name"];             //昵称

        // 如果还未成为供应商
        if ($supplier == 0) {
            return redirect("https://www.goooooo.top/index/personal/apply");
        }

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_loction-body',[
            'title'           => '卖家个人中心',
            'head_image_url'  => $head_image_url,
            'nick_name'       => $nick_name,
            'userMesg'        => $userMesg,
            ]);
    }
    // 个人信息修改
    public function changeMesg()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->get()) || !Cookie::has('id','user_') ) {
            echo -1;                            //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');   //获取cookie
        $type = $request->get( "type" );        //获取修改数据的类型
        $newMesg = $request->get( "newMesg" );  //新数据
        switch ( $type ) {
            case '1':
                $newMesg = strstr( $newMesg, 'uploads');
                // 修改头像
                Db::query("UPDATE `media_user` SET `head_image_url`=? WHERE `id`=?",[$newMesg,$user_id]);
                break;
            case '2':
                // 修改电话号码
                Db::query("UPDATE `media_user` SET `phone_number`=? WHERE `id`=?",[$newMesg,$user_id]);
                break;
            case '3':
                // 修改昵称
                Db::query("UPDATE `media_user` SET `nick_name`=? WHERE `id`=?",[$newMesg,$user_id]);
                break;
            default:
                # code...
                break;
        }
        echo 1;
    }

    // 修改我的地址
    public function storageLoction()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->get()) || !Cookie::has('id','user_') ) {
            echo -1;                                  //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');         //获取cookie
        $loction = $request->get( "loction" );        //获取地址信息
        Db::query("UPDATE `media_supplier` SET `loction`=? WHERE `id`=?",[$loction,$user_id]);
        echo 1;
    }
    // 修改我的介绍
    public function storageIntroduce()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                  //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');         //获取cookie
        $business_introduce = $request->post( "business_introduce" );        //获取地址信息
        Db::query("UPDATE `media_supplier` SET `business_introduce`=? WHERE `id`=?",[$business_introduce,$user_id]);
        echo 1;
    }

    // 发布商品处理
    public function release()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');                    //获取cookie
        $product_name = $request->post( "product_name" );        //获取地址信息
        $product_details = $request->post( "product_details" );  //商品描述
        $product_type = $request->post( "product_type" );        //商品属性
        $product_url1 = $request->post( "product_url1" );        //商品展示图1
        $product_url2 = $request->post( "product_url2" );        //商品展示图2
        $product_url3 = $request->post( "product_url3" );        //商品展示图3
        $product_url4 = $request->post( "product_url4" );        //商品展示图4
        $product_url5 = $request->post( "product_url5" );        //商品展示图5
        $product_state = 1;                                      //商品状态

        // 密码字符集，可任意添加你需要的字符 
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789'; 
        $length = 6;                                            //商品id长度
        $product_id = '';                                       //商品id
        for ( $i = 0; $i < $length; $i++ ) 
        { 
        // 这里提供两种字符获取方式 
        // 第一种是使用 substr 截取$chars中的任意一位字符； 
        // 第二种是取字符数组 $chars 的任意元素 
        // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1); 
        $product_id .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
        } 

        // 写入数据库
        Db::query("INSERT INTO `media_provide`(`id`, `product_id`, `product_name`, `product_url1`, `product_url2`, `product_url3`, `product_url4`, `product_url5`, `product_details`, `product_type`, `product_state`) VALUES (?,?,?,?,?,?,?,?,?,?,?)",[$user_id,$product_id,$product_name,$product_url1,$product_url2,$product_url3,$product_url4,$product_url5,$product_details,$product_type,$product_state]);
        echo 1;
    }

    // 修改商品信息处理
    public function acknowledgement()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');                    //获取cookie
        $product_id = $request->post( "product_id" );            //获取商品id
        $product_name = $request->post( "product_name" );        //商品名称
        $product_details = $request->post( "product_details" );  //商品描述
        $product_type = $request->post( "product_type" );        //商品属性
        $product_url1 = $request->post( "product_url1" );        //商品展示图1
        $product_url2 = $request->post( "product_url2" );        //商品展示图2
        $product_url3 = $request->post( "product_url3" );        //商品展示图3
        $product_url4 = $request->post( "product_url4" );        //商品展示图4
        $product_url5 = $request->post( "product_url5" );        //商品展示图5
        Db::query("UPDATE `media_provide` SET `product_name`=?,`product_url1`=?,`product_url2`=?,`product_url3`=?,`product_url4`=?,`product_url5`=?,`product_details`=?,`product_type`=? WHERE `id`=? and `product_id`=?",[$product_name,$product_url1,$product_url2,$product_url3,$product_url4,$product_url5,$product_details,$product_type,$user_id,$product_id]);
        echo 1;
    }

    // 下线处理函数
    public function offline()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');                    //获取cookie
        $product_id = $request->post( "product_id" );            //获取商品id
        Db::query("UPDATE `media_provide` SET `product_state`='0' WHERE `id`=? and `product_id`=?",[$user_id,$product_id]);
        echo 1;
    }

    // 上线处理函数
    public function goOnline()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');                    //获取cookie
        $product_id = $request->post( "product_id" );            //获取商品id
        Db::query("UPDATE `media_provide` SET `product_state`='1' WHERE `id`=? and `product_id`=?",[$user_id,$product_id]);
        echo 1;
    }

    // 商家发布动态
    public function publishdynamic()
    {
        // 渲染模板输出
        $view = new View();
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

       if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch("index/login");
        }
        $user_id = Cookie::get('id','user_');    //获取cookie

        // 获取动态数据
        $Dynamicdata = Loader::model('Dynamicdata');
        $dynamic_data = $Dynamicdata->getDyData( $user_id,1,5 );

        // 获取收藏列表
        $Collection = Loader::model('Collection');
        $CollectionData = $Collection->getCollectionData( $user_id,"dy" );

        // 获取他人评论未处理数
        $qt_com_number_arr = Db::query("select count(`id`) from `media_dynamic` WHERE `correspond_user_id`=? and `type`=? and `status`=?",[$user_id,2,0]); 
        $qt_com_number = $qt_com_number_arr[0]["count(`id`)"];

        // 获取评论数据
        $com_data = Db::query("SELECT * FROM `media_dynamic` WHERE `correspond_user_id`=? and `type`=? and `status`=?",[$user_id,2,0]); 
        for ($i= 0;$i< count($com_data); $i++){ 
            $com_userMesg = Db::query("SELECT `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$com_data[$i]["user_id"]]);
            $com_data[$i]["userMesg"] = $com_userMesg[0];
            $correspond_data = Db::query("SELECT * FROM `media_dynamic` WHERE `id`=?",[$com_data[$i]["correspond_id"]]);
            $com_data[$i]["correspond_data"] = $correspond_data[0];
        } 
        // dump($com_data);


        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);
        
        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_publishdynamic-body',[
            'title'           => '动态发布',
            'userMesg'        => $userMesg,
            'dynamic_data'    => $dynamic_data,
            'CollectionData'  => $CollectionData,
            'qt_com_number'   => $qt_com_number,
            'com_data'        => $com_data,
            ]);
    }
    // 发布新动态
    public function newdynamic()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $imglist = $request->post( "imglist" );  
        $content = $request->post( "content" );  
        $time=date("Y-m-d H:i");
        // 对imglist进行处理
        $imglist_arry = explode(',',$imglist); 

        for ($i= 0;$i< count($imglist_arry); $i++){ 
            $imglist_a[$i] ="/" . strstr($imglist_arry[$i],"static");
        } 
        $imglist_string = implode(",", $imglist_a);
        // dump($imglist_a);
        // dump($imglist_string);
        Db::query("INSERT INTO `media_dynamic`(`type`, `user_id`, `content`, `imglist`, `time`, `status`) VALUES (?,?,?,?,?,?)",[1,$user_id,$content,$imglist_string,$time,1]);
        echo 1;
    }

    // 刷除动态
    public function deletedynamic()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $id = $request->post( "id" );  
        Db::query("DELETE FROM `media_dynamic` WHERE `id`=?",[$id]);
        echo 1;
    }

    // 标记为已经读
    public function hadRead()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $id = $request->post( "id" );  
        Db::query("UPDATE `media_dynamic` SET `status`=? WHERE `id`=?",[1,$id]);
        echo 1;
    }

    // 我是代养人
    public function substitutes($choose1)
    {
        $IndexModel = Loader::model('IndexModel');
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        $view = new View();
        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        // dump($attentionUser);
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        switch ($choose1) {
            case 'substitutes':
                $title = '我是代养人';
                // 获取我发布的代养
                $substitutesData = Db::query("SELECT `id`, `substitutes_id`, `title`, `label1`, `label2`, `label3`, `label4`,`cover_img`, `time`, `announcer_id` FROM `media_substitutes` WHERE `announcer_id`=?",[$userMesg["id"]]);
                // dump($substitutesData);

                $view->engine->layout('layout/personal-layout');
                return $view->fetch('index/personal_substitutes-body',[
                    'title'           => $title,
                    'userMesg'        => $userMesg,
                    'substitutesData' => $substitutesData,
                    ]);
                break;

            case 'newsubstitutes':
                $title = '我是代养人';
                $view->engine->layout('layout/personal-layout');
                return $view->fetch('index/personal_newsubstitutes-body',[
                    'title'           => $title,
                    'userMesg'        => $userMesg,
                    ]);
                break;

            default:
                break;
        }
    }

    // 我的代养
    public function myadpotion($choose2)
    {
        $IndexModel = Loader::model('IndexModel');
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view = new View();
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        switch ($choose2) {
            case 'myadpotion':
                $title = '认养订单';
                // 获取认养订单数据
                $OrderModel = Loader::model('OrderModel');
                $mySuccSubOrder = $OrderModel->getMyOrder( $user_id,2,1 );
                $myNoSuccSubOrder = $OrderModel->getMyOrder( $user_id,2,0 );

                $view->engine->layout('layout/personal-layout');
                return $view->fetch('index/personal_myadoption-body',[
                    'title'           => $title,
                    'userMesg'        => $userMesg,
                    'mySuccSubOrder'  => $mySuccSubOrder,
                    'myNoSuccSubOrder' => $myNoSuccSubOrder,
                    ]);
                break;

            default:
                break;
        }
    }

    // 重新编辑代养信息
    public function editsub($substitutes_id)
    {
        $IndexModel = Loader::model('IndexModel');
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        // dump($attentionUser);
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view = new View();
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        // 获取具体代养数据
        $subData = Db::query("SELECT * FROM `media_substitutes` WHERE `substitutes_id`=?",[$substitutes_id]);
        $subData = $subData[0];
        $qtUserMesg = $IndexModel->getIdMesg($subData["announcer_id"]);

        // 非发布者不可编辑
        if ($qtUserMesg["id"] == $user_id) {
            $edit = 1;
        }else{
            $edit = 0;
        }

        $title = '重新编辑代养信息';
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/editsub',[
            'title'           => $title,
            'userMesg'        => $userMesg,
            'subData'         => $subData,
            'edit'            => $edit,
            'substitutes_id'  => $substitutes_id,
            ]);
    }

    // 信息通知
    public function innerMsg($type)
    {
        $IndexModel = Loader::model('IndexModel');
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view = new View();
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        switch ($type) 
        {
            case 'unread':
                $title = '未读消息';
                $noticeData = $sendNotice->getNotice(0,$user_id);
                // dump($noticeData);
                break;;

            case 'read':
                $title = '已经读信息';
                $noticeData = $sendNotice->getNotice(1,$user_id);
                break;
        }

        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_innerMsg-body',[
            'title'           => $title,
            'userMesg'        => $userMesg,
            'noticeData'      => $noticeData,
            ]);
    }

    // 人脸识别登录授权
    public function faceAuth()
    {
        // 渲染模板输出
        $view = new View();
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber($user_id);
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/personal_faceAuth-body',[
            'title'           => '申请成为供应商',
            'userMesg'        => $userMesg,
            'user_id'         => $user_id,
            ]);
    }
}