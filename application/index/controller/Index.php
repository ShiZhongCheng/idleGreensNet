<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;
// use think\Session;

class Index
{
    public function test()
    {
        $Comment = Loader::model('Comment');
        dump( $Comment->getHotDy() );
    }
    public function index()
    {
        $IndexModel = Loader::model('IndexModel');
        // 获取cookie user_id
        $user_id = $IndexModel->getCookie();

        // 等于-1表示没有获取cookie user_id
        switch ($user_id)
        {
        case -1:
            $login_loction = $IndexModel->getUserLoction();
            if ($login_loction == -1) {
                // $view = new View();
                // return $view->fetch('login_loction_no_id');
                $login_loction = "120.34537,30.3174";
            }
            // 获取附近供应商数据
            $page_mesg = $IndexModel->loginLonctionNoId( $login_loction ,10);                   
          break;
        default:
            // 获取附近供应商数据
            $page_mesg = $IndexModel->loginLonction( $user_id );   
            // 等于-1表示用户上次登录位置为空，需要定位
            if ($page_mesg == -1) {
                $view = new View();
                return $view->fetch('login_loction');
            }
            // 上次登录位置坐标
            $login_loction = $page_mesg["login_loction"];
          break;
        }

        // 获取出众农产品
        $Comment = Loader::model('Comment');
        $goodSell = $Comment->getGooDSell();
        $hotDy = $Comment->getHotDy();
        $fosterData = $Comment->indexFoster();
        
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();
        // 随机获取数据库数据
        $randProvide = $IndexModel->randProvide();
        // 产生轮播图背景
        $bgArry = $IndexModel->bgArry(3);

        $view = new View();

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
        $view->engine->layout('layout/sell-layout');
        return $view->fetch('index',[
            'title'           => '闲菜网',
            'userMesg'        => $userMesg,
            'login_loction'   => $login_loction,
            'page_mesg'       => $page_mesg ,
            'randProvide'     => $randProvide,
            'bgArry'          => $bgArry,
            'goodSell'        => $goodSell,
            'hotDy'           => $hotDy,
            'fosterData'      => $fosterData,
            ]);
    }
    // 供应商品页面
    public function gongying( $product_id )
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取全部分类
        $SellModel = Loader::model('SellModel');
        $classify = $SellModel->allClassify();

        // 商品信息
        $product = Db::query("SELECT * FROM `media_provide` WHERE `product_id`=?",[$product_id]);
        $product = $product[0];

        // 获取用户信息
        $user_mesg = Db::query("SELECT `name`,`phone_number` FROM `media_user` WHERE `id`=?",[$product["id"]]);
        $user_mesg = $user_mesg[0];

        //开启模板布局
        $view = new View();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber( $user_id );
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        $view->engine->layout('layout/personal-layout');
        return $view->fetch('gongying',[
            'title'           => '供应商品页面',
            'userMesg'        => $userMesg,
            'product'         => $product,
            'classify'        => $classify,
            'user_mesg'       => $user_mesg,
            ]);
    }
    public function login()
    {
        // 渲染模板输出
        $view = new View();
        return $view->fetch('login');
    }
     public function register()
    {
        // session_start();
        // echo $_SESSION['captcha_code'];
        // 用于生成验证码
        $rand = rand();

        // 渲染模板输出
        $view = new View();
        return $view->fetch('register',[
            'rand'       => $rand,
            ]);
    }
    public function loginLoction()
    {
        // 渲染模板输出
        $view = new View();
        return $view->fetch('login_loction');
    }
    public function saveLoginLoction()
    {
        //获取数据
        $request = Request::instance();
        if ( empty($request->get()) ) {
            echo -1;                            //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');   //获取cookie
        $loction = $request->get( "location" );  //获取位置信息

        // 没有登录
        if ( !Cookie::has('id','user_') ) {
            // cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>'','path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('loction');
            Cookie::set('loction',$loction );
            echo 1;
            exit(0);
        }
        Db::query("UPDATE `media_user` SET `login_loction`=? WHERE `id`=?",[$loction,$user_id]);
        echo 1;
    }
    public function sell()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取全部分类
        $SellModel = Loader::model('SellModel');
        $classify = $SellModel->allClassify();

        // 获取分页数据
        $classification = $SellModel->peoductData();

        //开启模板布局
        $view = new View();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();
        $strangerUser = $Chat->getStrangerUrMeg();
        // 获取未读消息数目
        $sendNotice = Loader::model('sendNotice');
        $noticeNotReadNumber = $sendNotice->getNotReadNumber( $user_id );
        $view->assign([
            'attentionUser'  => $attentionUser,
            'strangerUser' => $strangerUser,
            'noticeNotReadNumber' => $noticeNotReadNumber,
        ]);

        $view->engine->layout('layout/sell-layout');
        return $view->fetch('sell-body',[
            'title'           => '供应大厅',
            'userMesg'        => $userMesg,
            'classify'        => $classify,
            'classification'  => $classification,
            ]);
    }
    // 卖家个人
    public function seller( $id )
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取全部分类
        $SellModel = Loader::model('SellModel');
        $classify = $SellModel->allClassify();

        $Comment = Loader::model('Comment');
        $business_mesg = $Comment->sellerData($id);

        // 查询状态为1的用户数据 并且每页显示10条数据
        $list = Db::name('media_provide')->where('id',$id)->paginate(5);
        // 获取分页显示
        $page = $list->render();
        // dump($list);

        if (!empty($business_mesg["loction"])) {
            // 获取附近供应商数据（用于地址推荐）
            $nearbySupplier = $IndexModel->loginLonctionNoId( $business_mesg["loction"],4 );
        }else{
            $nearbySupplier = "";
        }
        // dump($nearbySupplier);

        // 获取动态数据
        $Dynamicdata = Loader::model('Dynamicdata');
        $dynamic_data = $Dynamicdata->getDyData( $id,1,5 );

        // 获取发布过的代养
        $FosterModel = Loader::model('FosterModel');
        $fosterData = $FosterModel->getUserFoster($id);
        // dump($fosterData);

        // 获取收藏列表
        $Collection = Loader::model('Collection');
        $CollectionData = $Collection->getCollectionData( "dy" );
        // dump($CollectionData);
        $userCollectionData = $Collection->getCollectionData( "ur" );
        // dump($userCollectionData);


        // 获取funshow
        $request = Request::instance();
        if ( empty($request->get("funshow")) ) {
            $funshow = 0;
        }else{
            $funshow = $request->get("funshow");
        }

        // 获取当前完整url
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        $backurl = $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
 
        //开启模板布局
        $view = new View();

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

        $view->engine->layout('layout/sell-layout');
        return $view->fetch('seller-body',[
            'title'               => '供应商个人主页',
            'id'                  => $id,
            'userMesg'            => $userMesg,
            'qt_nick_name'        => $business_mesg["nick_name"],
            'qt_head_image_url'   => $business_mesg["head_image_url"],
            'business_introduce'  => $business_mesg["business_introduce"],
            'loction'             => $business_mesg["loction"],
            'business_mesg'       => $business_mesg,
            'list'                => $list,
            'page'                => $page,
            'classify'            => $classify,
            'nearbySupplier'      => $nearbySupplier,
            'dynamic_data'        => $dynamic_data,
            'CollectionData'      => $CollectionData,
            'funshow'             => $funshow,
            'backurl'             => $backurl,
            'userCollectionData'  => $userCollectionData,
            'fosterData'          => $fosterData,
            ]);
    }

    // 搜索
    public function search()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        // 获取cookie user_id
        $user_id = $IndexModel->getCookie();

        // 获取全部分类
        $SellModel = Loader::model('SellModel');
        $classify = $SellModel->allClassify();

        // 获取收藏列表
        $Collection = Loader::model('Collection');
        $CollectionData = $Collection->getCollectionData( $user_id,"dy" );

        // 获取搜索数据 
        $Comment = Loader::model('comment');
        $search_page_mesg = $Comment->searchData();

        // 获取关注用户信息
        $Chat = Loader::model('Chat');
        $attentionUser = $Chat->getCollUrMeg();

        //开启模板布局
        $view = new View();

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

        $view->engine->layout('layout/personal-layout');
        return $view->fetch('search',[
            'title'              => '闲菜网站内搜索',
            'userMesg'           => $userMesg,
            'search_page_mesg'   => $search_page_mesg,
            'CollectionData'     => $CollectionData,
            'classify'           => $classify,
            'attentionUser'      => $attentionUser,
            ]);
    }

    // 动态
    public function dynamic()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        //开启模板布局
        $view = new View();

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

        $view->engine->layout('layout/personal-layout');
        return $view->fetch('dynamic',[
            'title'              => '商家动态',
            'userMesg'        => $userMesg,
            ]);
    }

    // 动态详情
    public function dynamic_details( $id )
    {
        $IndexModel = Loader::model('IndexModel');
        // 获取cookie user_id
        $user_id = $IndexModel->getCookie();
        $vo = Db::query("SELECT * FROM `media_dynamic` WHERE `id`=?",[$id]);
        $vo = $vo[0];
        $vo["imglist"] = explode(',',$vo["imglist"]);
        array_pop( $vo["imglist"] ); 

        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();

        // 获取收藏列表
        $Collection = Loader::model('Collection');
        $CollectionData = $Collection->getCollectionData( "dy" );

        // 获取发动态者信息
        $qtuser_id = $vo["user_id"];
        $qtuserMesg = Db::query("SELECT `id`,`name`,`nick_name`, `head_image_url`, `supplier` FROM `media_user` WHERE `id`=?",[$qtuser_id]);
        $qtuserMesg = $qtuserMesg[0];

        // 更新阅读数据
        $reading_number_arr = Db::query("SELECT `reading_number` FROM `media_dynamic` WHERE `id`=?",[$id]);
        if ($reading_number_arr[0]["reading_number"] == "") {
            $reading_number = 1;
        }else{
            $reading_number = $reading_number_arr[0]["reading_number"] + 1;
        }
         Db::query("UPDATE `media_dynamic` SET `reading_number`=? WHERE `id`=?",[$reading_number,$id]);

        // 获取评论信息
        $comment_data = Db::query("SELECT `user_id`, `correspond_id`, `content`, `time` FROM `media_dynamic` WHERE `correspond_id`=? and `type`=?",[$id,2]);
        for ($i= 0;$i< count($comment_data); $i++){ 
            $com_userMesg = Db::query("SELECT `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$comment_data[$i]["user_id"]]);
            $comment_data[$i]["userMesg"] = $com_userMesg[0];
        } 

        //开启模板布局
        $view = new View();

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

        $view->engine->layout('layout/personal-layout');
        return $view->fetch('dynamic_details',[
            'title'              => '动态详情',
            'vo'                 => $vo,
            'id'                 => $id,
            'qtuserMesg'         => $qtuserMesg,
            'CollectionData'     => $CollectionData,
            'userMesg'           => $userMesg,
            'comment_data'       => $comment_data,
            ]);
    }

    // 评论
    public function discuss()
    {
        //获取数据
        $request = Request::instance();
        if (empty($request->POST()) || !Cookie::has('id','user_') ) {
            echo -1;                                  //没有获取到数据
            exit(0);
        }
        // 判断是否有answer参数
        if (!empty( $request->post( "answer" ) )) {
            Db::query("UPDATE `media_dynamic` SET `status`=? WHERE `id`=?",[1,$request->post( "correspond_id" )]);
        }

        $user_id = Cookie::get('id','user_');    //获取cookie
        $correspond_id = $request->post( "correspond_id" );   
        $content = $request->post( "content" );
        $time=date("Y-m-d H:i");
        // 获取此动态发布者
        $correspond_user_id_arr = Db::query("SELECT `user_id` FROM `media_dynamic` WHERE `id`=?",[$correspond_id]);
        $correspond_user_id = $correspond_user_id_arr[0]["user_id"];

        Db::query("INSERT INTO `media_dynamic`(`type`, `user_id`,`correspond_user_id`, `correspond_id`, `content`, `time`, `status`) VALUES (?,?,?,?,?,?,?)",[2,$user_id,$correspond_user_id,$correspond_id,$content,$time,0]);

        // 修改评论条数
        $com_number_arr = Db::query("SELECT `coment_number` FROM `media_dynamic` WHERE `id`=?",[$correspond_id]);
        $com_number = $com_number_arr[0]["coment_number"]+1;
        Db::query("UPDATE `media_dynamic` SET `coment_number`=? WHERE `id`=?",[$com_number,$correspond_id]);
        echo 1;
    }

    // 更多供应商
    public function more_supplier()
    {
        $IndexModel = Loader::model('IndexModel');
        // 用户信息
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();

        // 获取行政区域坐标
        $request = Request::instance();
         if (!empty( $request->get( "address" ) )) {
            $addres = $request->get( "address" );
        }else{
            $addres = "浙江 杭州 江干区 高沙农贸市场";
        }
        $address = explode(' ',$addres);
        $Curl = Loader::model('Curl');
        $location = $Curl->curlGet($address);   //坐标
        // 获取附近供应商数据
        $page_mesg = $IndexModel->loginLonctionNoId( $location,20 );
        // dump($page_mesg);

        //开启模板布局
        $view = new View();

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
        
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('more_supplier',[
            'title'              => '更多供应商',
            'userMesg'           => $userMesg,
            'address'            => $address,
            'addres'             => $addres,
            'page_mesg'          => $page_mesg,
            ]);
    }

    // 获取具体代养信息
    public function subshow($substitutes_id)
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

        // 获取成长历程
        $Getgrowthdata = Loader::model('Getgrowthdata');
        $growth = $Getgrowthdata->growthData(5,$substitutes_id,1);
        // 获取成长历程评论
        $Getgrowthdata = Loader::model('Getgrowthdata');
        $growthComment = $Getgrowthdata->getGrowthComment(6,$substitutes_id,1);
        // 获取买者信息
        $buyerData =  $Getgrowthdata->getBuyer( $substitutes_id );

        // dump($substitutes_id);
        // 获取具体代养数据
        $subData = Db::query("SELECT * FROM `media_substitutes` WHERE `substitutes_id`=?",[$substitutes_id]);
        $subData = $subData[0];
        $qtUserMesg = $IndexModel->getIdMesg($subData["announcer_id"]);

        $title = '代养信息展示';
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/subshow',[
            'title'           => $title,
            'userMesg'        => $userMesg,
            'subData'         => $subData,
            'qtUserMesg'      => $qtUserMesg,
            'substitutes_id'  => $substitutes_id,
            'growth'          => $growth,
            'growthComment'   => $growthComment,
            'buyerData'       => $buyerData,
            ]);
    }

    // 代养大厅
    public function foster()
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

        // 获取数据
        $FosterModel = $IndexModel = Loader::model('FosterModel');
        $fosterData = $FosterModel->getFosterData();
        // dump($fosterData);

        $title = '代养信息展示';
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('index/foster',[
            'title'           => $title,
            'userMesg'        => $userMesg,
            'fosterData'      => $fosterData,
            ]);
    }

    // 新建数据表
    public function create()
    {
        // 供应商表
        $media_supplier = "create table media_supplier (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            id varchar(100) comment '商家ID',
            loction varchar(100) comment '商家定位',
            business_introduce text(1200) comment '商家介绍',
            statistical_table varchar(255) comment '统计表',
            currently_available varchar(255) comment '当前提供表',
            comment varchar(255) comment '用户评价表',
            purchase_notes varchar(100) comment '购买须知表')
            DEFAULT CHARACTER SET=utf8";
        // Db::query( $media_supplier );
        // dump("供应商表创建ok");

        // 统计表
        $media_statistics = "create table media_statistics (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            id varchar(100) comment '商家ID',
            trading_volume varchar(50) comment '交易量',
            comprehensive_evaluation varchar(50) comment '综合评价',
            evaluate_total_number varchar(50) comment '评价总人数',
            one_point_rating varchar(50) comment '一分评价人数',
            two_point_rating varchar(50) comment '两分评价人数',
            three_point_rating varchar(50) comment '三分评价人数',
            four_point_rating varchar(50) comment '四分评价人数',
            five_point_rating varchar(50) comment '五分评价人数')
            DEFAULT CHARACTER SET=utf8";
        // Db::query( $media_statistics );   
        // dump("统计表创建ok");

        // 当前提供表
        $media_provide = "create table media_provide (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            id varchar(100) comment '商家ID',
            product_id varchar(100) comment '商品编号',
            product_url1 varchar(100) comment '供应商品展示图1',
            product_url2 varchar(100) comment '供应商品展示图2',
            product_url3 varchar(100) comment '供应商品展示图3',
            product_url4 varchar(100) comment '供应商品展示图4',
            product_url5 varchar(100) comment '供应商品展示图5',
            product_details varchar(255) comment '供应商品详情',
            product_type varchar(100) comment '商品类型',
            product_state tinyint(1) DEFAULT 0 comment '商品状态')
            DEFAULT CHARACTER SET=utf8";
        // Db::query( $media_provide );   
        // dump("当前表创建ok");

        // 用户评论表
        $media_comment = "create table media_comment (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            be_evaluator_id varchar(100) comment '被评价商家ID',
            evaluator_id varchar(100) comment '评价者ID',
            evaluation_information varchar(255) comment '评价信息',
            point_rating varchar(100) comment '评价分数',
            evaluation_time varchar(100) comment '评价日期')
            DEFAULT CHARACTER SET=utf8";
        Db::query( $media_comment );
        dump( "用户评论表创建ok！" );

        // 购买须知表
        $media_purchase_notes = "create table media_purchase_notes (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            id varchar(100) comment '供应商ID',
            note1 varchar(150) comment '须知1',
            note2 varchar(150) comment '须知2',
            note3 varchar(150) comment '须知3',
            note4 varchar(150) comment '须知4',
            note5 varchar(150) comment '须知5',
            last_modified_time varchar(100) comment '上次修改时间')
            DEFAULT CHARACTER SET=utf8";
        Db::query( $media_purchase_notes );
        dump( "购买须知表创建ok" );

        // 我要买表
        $media_wanta_buy = "create table media_wanta_buy (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            id varchar(100) comment '发布者ID',
            demand_information varchar(255) comment '需求信息',
            common_telephone varchar(100) comment '常用电话',
            release_time varchar(100) comment '发布时间')
            DEFAULT CHARACTER SET=utf8";
        Db::query( $media_wanta_buy );
        dump( "我要买表创建ok" );

        // 我要卖表
        $media_wanta_sell = "create table media_wanta_sell (
            sort_id int unsigned not null auto_increment primary key comment '排序ID',
            id varchar(100) comment '发布者ID',
            product_img_url1 varchar(100) comment '商品展示图1',
            product_img_url2 varchar(100) comment '商品展示图2',
            product_img_url3 varchar(100) comment '商品展示图3',
            product_img_url4 varchar(100) comment '商品展示图4',
            product_img_url5 varchar(100) comment '商品展示图5',
            product_details varchar(255) comment '商品详情',
            product_type varchar(100) comment '商品分类',
            release_time varchar(100) comment '发布时间',
            common_telephone varchar(100) comment '常用电话',
            product_state tinyint(1) DEFAULT 0 comment '商品状态')
            DEFAULT CHARACTER SET=utf8";
        Db::query( $media_wanta_sell );
        dump( "我要卖表创建ok" );
    }
}
