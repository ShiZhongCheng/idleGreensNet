<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Index
{
    public function index()
    {
        $request = Request::instance();
        if ( !empty($request->get()) && $request->get("remberPassword")=="true" ) {
            $nick_name = $request->get("nick_name");
            $password = $request->get("password");
            $id = $request->get("id");
            // cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>86400,'path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('id');
            Cookie::set('id',$id);
        }
        // 判断是否有cookie
        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch('login');
        }
        $user_id = Cookie::get('id','user_');    //获取cookie

        // 判断此次登录是否定位
        $login_loction = Db::query("SELECT `login_loction` FROM `media_user` WHERE `id`=?",[$user_id]);
        $login_loction = $login_loction[0]["login_loction"];
        if ($login_loction == "") {
           return $view->fetch('login_loction');
        }

        // 获取附近供应商
        //实例化GetDistance模型,用于计算两点之间的距离
        $GetDistance = Loader::model('GetDistance');
        // $distance = $GetDistance->getDistance($login_loction,'120.34217,30.31747');
        $all_supplier = Db::query("SELECT `id`, `loction` FROM `media_supplier` WHERE 1"); 
        $loction_supplier_id = array();             //相距在10000米以内的供应商id
        // 遍历所有供应商
        foreach ($all_supplier as $supplier) {
          if ( $GetDistance->getDistance($login_loction,$supplier["loction"]) < 10000 )  {
              $supplierA1 = array();
              $supplierA1["id"] = $supplier["id"];
              $supplierA1["distance"] = $GetDistance->getDistance($login_loction,$supplier["loction"]);
              $supplierA1["loction"] = $supplier["loction"];
              array_push($loction_supplier_id,$supplierA1);
          }
        }
        // 遍历附近供应商id数组
        $loction_supplier_mesg = array();           //相距在10000米以内的供应商信息
        foreach ($loction_supplier_id as $loction_supplier_user_id) {
           $loc_suuer_data = Db::query("SELECT `id`,`nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$loction_supplier_user_id["id"]]);
           $supplierA2 = array();
           $supplierA2["id"] = $loction_supplier_user_id["id"];
           $supplierA2["distance"] = $loction_supplier_user_id["distance"];
           $supplierA2["loction"] = $loction_supplier_user_id["loction"];
           $supplierA2["nick_name"] = $loc_suuer_data[0]["nick_name"];
           $supplierA2["head_image_url"] = $loc_suuer_data[0]["head_image_url"];
           array_push($loction_supplier_mesg,$supplierA2);
        }

        //实例化Pagination模型,用于分页
        //得到当前是第几页  
        if (isset ($_GET["page"])) { //是否存在"id"的参数  
            $page = $_GET["page"];  
        } else {  
            $page = 1;  
        }  
        $Pagination = Loader::model('Pagination');
        $page_mesg = $Pagination->pagination($loction_supplier_mesg,5,$page);   //分页数据


        $head_image_url = Db::query("SELECT `head_image_url` FROM `media_user` WHERE `id`=?",[$user_id]);
        $head_image_url = $head_image_url[0]["head_image_url"];

        // 随机获取数据库数据
        $randProvide = Db::query("SELECT * FROM `media_provide`  AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX(sort_id) FROM  `media_provide`)) AS sort_id) AS t2 WHERE t1.sort_id >= t2.sort_id ORDER BY t1.sort_id ASC LIMIT 7");

        // 产生轮播图背景
        $numbers = range (1,20); 
        //shuffle 将数组顺序随即打乱 
        shuffle ($numbers); 
        //array_slice 取该数组中的某一段 
        $num=3; 
        $bgArry = array_slice($numbers,0,$num); 

        //开启模板布局
        $view->engine->layout('layout/sell-layout');
        return $view->fetch('index',[
            'title'   => '闲菜网',
            'head_image_url'  => $head_image_url,
            'login_loction'   => $login_loction,
            'page_mesg'       => $page_mesg ,
            'randProvide'     => $randProvide,
            'randProvide1'    => json_encode($randProvide) ,
            'bgArry'          => $bgArry,
            ]);
    }
    // 供应商品页面
    public function gongying( $product_id )
    {
        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch('login');
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $head_image_url = Db::query("SELECT `head_image_url` FROM `media_user` WHERE `id`=?",[$user_id]);
        $head_image_url = $head_image_url[0]["head_image_url"];

        // 获取分类
        $classify = Db::query("SELECT `classify_id`, `classify_name` FROM `media_classify` WHERE 1");

        // 商品信息
        $product = Db::query("SELECT * FROM `media_provide` WHERE `product_id`=?",[$product_id]);
        $product = $product[0];

        // 获取用户信息
        $user_mesg = Db::query("SELECT `name`,`phone_number` FROM `media_user` WHERE `id`=?",[$product["id"]]);
        $user_mesg = $user_mesg[0];

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('gongying',[
            'title'           => '供应商品页面',
            'head_image_url'  => $head_image_url,
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
        // 渲染模板输出
        $view = new View();
        return $view->fetch('register');
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
        if (empty($request->get()) || !Cookie::has('id','user_') ) {
            echo -1;                            //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');   //获取cookie
        $loction = $request->get( "location" );  //获取位置信息
        Db::query("UPDATE `media_user` SET `login_loction`=? WHERE `id`=?",[$loction,$user_id]);
        echo 1;
    }
    public function sell()
    {
        $request = Request::instance();
        if ( !empty($request->get()) && $request->get("remberPassword")=="true" ) {
            $nick_name = $request->get("nick_name");
            $password = $request->get("password");
            $id = $request->get("id");
            // cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>86400,'path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('id');
            Cookie::set('id',$id);
        }else if ( !empty($request->get()) && $request->get("remberPassword")=="false" ) {
            $nick_name = $request->get("nick_name");
            $password = $request->get("password");
            $id = $request->get("id");
            // cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>'','path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('id');
            Cookie::set('id',$id);
        }
        // 判断是否有cookie
        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');   //登录
            return $view->fetch('login');
        }
        $user_id = Cookie::get('id','user_');    //获取cookie

        $head_image_url = Db::query("SELECT `head_image_url` FROM `media_user` WHERE `id`=?",[$user_id]);
        $head_image_url = $head_image_url[0]["head_image_url"];

        // 获取全部分类
        $classify = Db::query("SELECT * FROM `media_classify` WHERE 1");

        //实例化Supplyclassification模型
        if ( isset ($_GET["page"]) && isset ($_GET["type"]) ) {
               $page = $request->get("page");
               $type = $request->get("type");   
          } else{
               $page = 1;  
               $type = 1;  
          }
        if ( isset ($_GET["page"]) && isset ($_GET["type"]) ) {
               $page = $request->get("page");
               $type = $request->get("type");   
          } else{
               $page = 1;  
               $type = 1;  
          }  
        $Supplyclassification = Loader::model('Supplyclassification');
        $classification = $Supplyclassification->classification(12,$page,$type);   //分页数据

        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('sell-body',[
            'title'   => '供应大厅',
            'head_image_url'  => $head_image_url,
            'classify'        => $classify,
            'classification'  => $classification,
            ]);
    }
    // 卖家个人
    public function seller( $id )
    {
        // 渲染模板输出
        $view = new View();
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');              //登录
            return $view->fetch('login');
        }
        $user_id = Cookie::get('id','user_');                         //获取cookie
        $user_mesg = Db::query("SELECT `head_image_url` FROM `media_user` WHERE `id`=?",[$user_id]);
        $head_image_url = $user_mesg[0]["head_image_url"];            //头像

        $qt_user_id = $id;
        $qt_user_mesg = Db::query("SELECT `head_image_url`,`nick_name` FROM `media_user` WHERE `id`=?",[$qt_user_id]);
        if (empty( $qt_user_mesg )) {
            return redirect("https://www.goooooo.top/sell");           //判断路径的user_id是否存在
        }
        $qt_head_image_url = $qt_user_mesg[0]["head_image_url"];       //商家头像
        $qt_nick_name = $qt_user_mesg[0]["nick_name"];                 //商家昵称

        $business_mesg = Db::query("SELECT `business_introduce`,`loction` FROM `media_supplier` WHERE `id`=?",[$qt_user_id]);
        if (empty( $business_mesg )) {
            return redirect("https://www.goooooo.top/sell");           //判断商家信息是否为空
        }
        $business_introduce = $business_mesg[0]["business_introduce"]; //商家介绍
        $loction = $business_mesg[0]["loction"];                       //商家地址

        // 查询状态为1的用户数据 并且每页显示10条数据
        $list = Db::name('media_provide')->where('id',$id)->paginate(5);
        // 获取分页显示
        $page = $list->render();

        // 获取分类
        $classify = Db::query("SELECT `classify_id`, `classify_name` FROM `media_classify` WHERE 1");
 
        //开启模板布局
        $view->engine->layout('layout/sell-layout');
        return $view->fetch('seller-body',[
            'title'               => '供应商个人主页',
            'head_image_url'      => $head_image_url,
            'qt_nick_name'        => $qt_nick_name,
            'qt_head_image_url'   => $qt_head_image_url,
            'business_introduce'  => $business_introduce,
            'loction'             => $loction,
            'list'                => $list,
            'page'                => $page,
            'user_id'             => $user_id,
            'classify'            => $classify,
            ]);
    }

    // 搜索
    public function search()
    {
        if ( !Cookie::has('id','user_') ) {
            // return $view->fetch('login@index/login');              //登录
            return $view->fetch('login');
        }
        $user_id = Cookie::get('id','user_');                         //获取cookie
        $user_mesg = Db::query("SELECT `head_image_url` FROM `media_user` WHERE `id`=?",[$user_id]);
        $head_image_url = $user_mesg[0]["head_image_url"];            //头像

        $request = Request::instance();
        $q = $request->get("q");
        // 得到当前type
        if (isset ($_GET["type"])) { //是否存在"id"的参数  
            $type = $_GET["type"];  
        } else {  
            $type = 1;  
        }
        //得到当前是第几页  
        if (isset ($_GET["page"])) { //是否存在"id"的参数  
            $page = $_GET["page"];  
        } else {  
            $page = 1;  
        }  

        $Search = Loader::model('Search');
        $search_page_mesg = $Search->search(5,$page,$type,$q);   //分页数据

        // 获取分类
        $classify = Db::query("SELECT `classify_id`, `classify_name` FROM `media_classify` WHERE 1");

        // 渲染模板输出
        $view = new View();
        //开启模板布局
        $view->engine->layout('layout/personal-layout');
        return $view->fetch('search',[
            'title'   => '闲菜网站内搜索',
            'head_image_url'      => $head_image_url,
            'search_page_mesg'   => $search_page_mesg,
            'classify'           => $classify,
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
