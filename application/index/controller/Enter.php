<?php
namespace app\index\controller;
use think\View;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;
use \think\File;

class Enter
{
    public function remberUser()
    {
        $IndexModel = Loader::model('IndexModel');
        // setCookie
        $IndexModel->setCookie();
    }
	 public function registerMesg()
    {
        // 获取post数据
        $request = Request::instance();
        $face_token = $request->post("face_token");
        $name = $request->post("name");
        $password = $request->post("password");
        $password = md5( $password );                              //MD5编码密码
        if ( $face_token=="" ) {
            echo "没有获取到face_token";
            exit(0);
        }

        // 操作
        Db::query("INSERT INTO `media_user`(`face_token`,`name`,`password`) VALUES (?,?,?)",[$face_token,$name,$password]);
        $data = Db::query("SELECT `id`, `name`, `password` FROM `media_user` WHERE `face_token`=?",[$face_token]);

        if (empty($data)) {
            echo "注册失败！";
            exit(0);
        }
        echo 1;
    }
    // 临时注册
    public function nowregister()
    {
        // 获取post数据
        $request = Request::instance();
        if (empty( $request->post() )) {
            return "没有获取到数据"; 
        }
        $name = $request->post("name");
        $phone_number = $request->post("phone_number");
        $nick_name = $request->post("nick_name");
        $password = md5( "123456" );
        Db::query("INSERT INTO `media_user`(`name`, `password`, `phone_number`, `nick_name`) VALUES (?,?,?,?)",[$name,$password,$phone_number,$nick_name]);
        return "注册成功！";
    }
    public function getFaceTokenMesg()
    {
    	// 获取post数据
        $request = Request::instance();
        $face_token = $request->post("face_token");
        if ( $face_token=="" ) {
            echo "匹配到的face_token传值错误！";
            exit(0);
        }

        // 操作
        $data = Db::query("SELECT * FROM `media_user` WHERE `face_token`=?",[$face_token]);
        if ( empty($data) ) {
            echo "没有获取到后台数据";
            exit(0);
        }
        $backData = array('id'=>$data[0]["id"],'face_token'=>$data[0]["face_token"],'name'=>$data[0]["name"],"password"=>$data[0]["password"]);
        echo json_encode($backData);
    }
    public function enterUserMesg()
    {
        // 获取post数据
        $request = Request::instance();
        $name = $request->post("name");
        $password = $request->post("password");
        if ($name == "" || $password == "") {
            echo -1;
            exit(0);
        }

        // 操作
        $data = Db::query("SELECT `id`, `nick_name`, `password` FROM `media_user` WHERE `nick_name`=? or `phone_number`=?",[$name,$name]);
        if (empty( $data )) {
           echo 1;
           exit(0);
        }
        $data_password = $data[0]["password"];
        if ( md5($password) != $data_password) {
            echo 0;
            exit(0);
        }
        echo json_encode( $data );
    }

    // 短信测试
    public function sendMesg()
    {
        if ( !Cookie::has('id','user_') ) {
            return -1;
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $request = Request::instance();
        $RecNum = $request->get("RecNum");       // 接受人联系电话
        $product_id = $request->get("product_id");  // 获取商品ID

        $user_data = Db::query("SELECT `name`, `phone_number` FROM `media_user` WHERE `id`=?",[$user_id]);
        $name = $user_data[0]["name"]."先生";
        $phone_number = $user_data[0]["phone_number"];
        $time = date("H:i:s");
        
        $SignName = "闲菜网";
        $TemplateCode = "SMS_78735007";
        $ParamString = "{'product_id':'".$product_id."','time':'".$time."','name':'".$name."','phone_number':'".$phone_number."'}";

        $Singlesendsms = Loader::model('Singlesendsms');
        $mesg = $Singlesendsms->sendMesg($ParamString,$RecNum,$SignName,$TemplateCode); 
        // dump( $ParamString );
        return 1;
    }

    // 收藏处理
    public function collection()
    {
         //获取数据
        $request = Request::instance();
        if (empty($request->post()) || !Cookie::has('id','user_') ) {
            echo -1;                                             //没有获取到数据
            exit(0);
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        $data = $request->post( "data" );  
        $myOldColl = Db::query("SELECT `collection` FROM `media_user` WHERE `id`=?",[$user_id]);
        // 重新拼接
        $newColl = $myOldColl[0]["collection"].$data.",";
        Db::query("UPDATE `media_user` SET `collection`=? WHERE `id`=?",[$newColl,$user_id]);
        echo 1;
    }

    // 退出
    public function dropout()
    {
        $IndexModel = Loader::model('IndexModel');
        $IndexModel->delCookie();
        echo 1;
    }

    // 上传主页背景大图
    public function upload()
    {
        $user_id = Cookie::get('id','user_');   //获取cookie
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');

        //获取数据
        $req = Request::instance();
        $url = $req->post("url");

        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $fileRoot = 'uploads/'.str_replace('\\','/',$info->getSaveName());
            Db::query("UPDATE `media_user` SET `my_home_page_bg`=? WHERE `id`=?",[$fileRoot,$user_id]);
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
       return redirect($url);
    }

    // supplier 图片上传
    public function supplier_upload()
    {
        $request = Request::instance();

        $file = request()->file('cover_image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $fileRoot = 'uploads/'.str_replace('\\','/',$info->getSaveName());

            $image = \think\Image::open($fileRoot);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
            $image->thumb(150,150,\think\Image::THUMB_CENTER)->save( $fileRoot.'thumb.png');
            echo $fileRoot;
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    // subshow_upload
    public function subshow_upload()
    {
        $request = Request::instance();

        $file = request()->file('cover_image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $fileRoot = 'uploads/'.str_replace('\\','/',$info->getSaveName());

            $image = \think\Image::open($fileRoot);
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
            $image->thumb(150,150,\think\Image::THUMB_CENTER)->text('闲菜网','./static/font/zhaozi.otf',13,'#ffffff')->save( $fileRoot.'thumb.png');
            echo $fileRoot.'thumb.png';
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    // 发送私信
    public function sendPrivateLetter()
    {
        // 接收数据
        $request = Request::instance();
        $receive_id = $request->post( "aims_id" );  
        $send_id = $request->post( "my_id" );  
        $content = $request->post( "content" ); 
        if ($receive_id == "0") {
            exit(0);
        }
        // 日期转换为UNIX时间戳
        $time = strtotime( date('Y-m-d H:i') );
        // 写入数据库
        Db::query("INSERT INTO `media_letter`(`receive_id`, `send_id`, `content`, `time`, `type`, `status`) VALUES (?,?,?,?,?,?)",[$receive_id,$send_id,$content,$time,1,0]);

        // 获取接收者和发送者信息
        $receiveUser = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$receive_id]);
        $sendUser = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$send_id]);
        $returnData = array();
        array_push($returnData,$receiveUser[0],$sendUser[0],$content,date('Y-m-d H:i', $time));
        echo json_encode($returnData);
        // dump($returnData);
    }
    // 获取双方通信记录
    public function getCommunicationRecord()
    {
        // 接收数据
        $request = Request::instance();
        $aims_id = $request->post( "aims_id" );  
        $my_id = $request->post( "my_id" );  

        // 发送给我的信息标记为已读
        Db:: query("UPDATE `media_letter` SET `status`=? WHERE `send_id`=? and `receive_id`=?",[1,$aims_id,$my_id]);

        $ISent = Db::query("SELECT * FROM `media_letter` WHERE `send_id`=? and `receive_id`=?",[$my_id,$aims_id]);
        $ASent = Db::query("SELECT * FROM `media_letter` WHERE `send_id`=? and `receive_id`=?",[$aims_id,$my_id]);

        $returnData = array();
        // 拼接数组
        foreach ($ISent as $I => $ID) {
            $ID["direction"] = "right";
            $ID["time"] = date('Y-m-d H:i', $ID["time"]);
            $send_id = $ID["send_id"];
            $sendUser = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$send_id]);
            $ID["sendUserMeg"] = $sendUser[0];
            array_push($returnData, $ID);
        }
        foreach ($ASent as $A => $AD) {
            $AD["direction"] = "left";
            $AD["time"] = date('Y-m-d H:i', $AD["time"]);
            $send_id = $AD["send_id"];
            $sendUser = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$send_id]);
            $AD["sendUserMeg"] = $sendUser[0];
            array_push($returnData, $AD);
        }

        $volume = array();
        foreach ($returnData as $key => $row)
        {
            $volume[$key]  = $row['time'];

        }
        array_multisort($volume, SORT_ASC, $returnData);
        // dump($returnData);

        echo json_encode($returnData);
    }

    // 实时获取信息
    public function realTimeGetMeg()
    {
        // 接收数据
        $request = Request::instance();
        $aims_id = $request->post( "aims_id" );  
        $my_id = $request->post( "my_id" ); 

        $returnData = array();

        switch ( $request->post( "way" ) ) {
            case '0':
                $AllSent = Db::query("SELECT * FROM `media_letter` WHERE `type`=? and `receive_id`=? and `status`!=? and `notiStaus`=? and `send_id` NOT BETWEEN ? and ?",[1,$my_id,1,0,$aims_id,$aims_id]);
                // 获取用户关注列表
                $Collection = Loader::model('Collection');
                $userCollection = $Collection->getCollectionData( "ur" );
                // 拼接数组
                foreach ($AllSent as $A => $AD) {
                    // // 发送给我的信息标记为已读
                    Db:: query("UPDATE `media_letter` SET `notiStaus`=? WHERE `type`=? and `id`=?",[1,1,$AD["id"]]);

                    // 查看此用户是否已经关注过
                    if (in_array($AD["send_id"],$userCollection)) 
                    { 
                        $AD["beColl"] = "yes";
                    }else 
                    { 
                        $AD["beColl"] = "no";
                    } 
                    $AD["direction"] = "left";
                    $AD["time"] = date('Y-m-d H:i', $AD["time"]);
                    $AD["Notification"] = "yes";
                    $send_id = $AD["send_id"];
                    $sendUser = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$send_id]);
                    $AD["sendUserMeg"] = $sendUser[0];
                    array_push($returnData, $AD);
                }
                break;

            case '1':
                $ASent = Db::query("SELECT * FROM `media_letter` WHERE `send_id`=? and `receive_id`=? and `status`=? and `type`=?",[$aims_id,$my_id,0,1]);
                // 发送给我的信息标记为已读
                Db:: query("UPDATE `media_letter` SET `status`=? WHERE `send_id`=? and `receive_id`=? and `status`=? and `type`=?",[1,$aims_id,$my_id,0,1]);
                // 拼接数组
                foreach ($ASent as $A => $AD) {
                    $AD["direction"] = "left";
                    $AD["time"] = date('Y-m-d H:i', $AD["time"]);
                    $AD["Notification"] = "no";
                    $send_id = $AD["send_id"];
                    $sendUser = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$send_id]);
                    $AD["sendUserMeg"] = $sendUser[0];
                    array_push($returnData, $AD);
                }
                break;

            default:
                break;
        }

        $volume = array();
        foreach ($returnData as $key => $row)
        {
            $volume[$key]  = $row['time'];

        }
        array_multisort($volume, SORT_ASC, $returnData);
        // dump($returnData);

        echo json_encode($returnData);
    }

    // 查找联系人
    public function lookUser()
    {
        // 接收数据
        $request = Request::instance();
        $nickName = $request->post( "nickName" ); 

        $returnData = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `nick_name` LIKE '%".$nickName."%'");
        echo json_encode($returnData);
    }

    // 发布新代养
    public function newSubstitutes()
    {
        // 接收数据
        $request = Request::instance();
        $title = $request->post( "title" ); 
        $labelData = $request->post( "labelData" ); 
        $coverUrl = $request->post( "coverUrl" ); 
        $content = $request->post( "content" ); 
        $quotes = $request->post( "quotes" );

        $labelData = explode(',',$labelData);
        $coverUrl = strstr($coverUrl, 'uploads');
        if ( empty($title) || empty($labelData) || empty($coverUrl) || empty($content) ) {
            echo -1;
            exit(0);
        }

        // 日期转换为UNIX时间戳
        $time = strtotime( date('Y-m-d H:i') );

        $IndexModel = Loader::model('IndexModel');
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();

        // 密码字符集，可任意添加你需要的字符 
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789'; 
        $length = 6;                                            //商品id长度
        $substitutes_id = '';                                       //商品id
        for ( $i = 0; $i < $length; $i++ ) 
        { 
            $substitutes_id .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
        } 

        for ($i=0; $i < 4; $i++) { 
            if ( !isset($labelData[$i]) ) {
                $labelData[$i] = "";
            }
        }

        Db::query("INSERT INTO `media_substitutes`(`substitutes_id`, `title`, `label1`, `label2`, `label3`, `label4`, `content`, `cover_img`,`quotes`,`time`, `announcer_id`) VALUES (?,?,?,?,?,?,?,?,?,?,?)",[$substitutes_id,$title,$labelData[0],$labelData[1],$labelData[2],$labelData[3],$content,$coverUrl,$quotes,$time,$userMesg["id"]]);
        
        echo json_encode(1);
    }
    // 发布新代养
    public function editSubstitutes()
    {
        // 接收数据
        $request = Request::instance();
        $title = $request->post( "title" ); 
        $labelData = $request->post( "labelData" ); 
        $coverUrl = $request->post( "coverUrl" ); 
        $content = $request->post( "content" ); 
        $substitutes_id = $request->post( "substitutes_id" ); 
        $quotes = $request->post( "quotes" ); 

        $labelData = explode(',',$labelData);
        $coverUrl = strstr($coverUrl, 'uploads');
        if ( empty($title) || empty($labelData) || empty($coverUrl) || empty($content) || empty($substitutes_id) ) {
            echo -1;
            exit(0);
        }

        for ($i=0; $i < 4; $i++) { 
            if ( !isset($labelData[$i]) ) {
                $labelData[$i] = "";
            }
        }

        Db::query("UPDATE `media_substitutes` SET `title`=?,`label1`=?,`label2`=?,`label3`=?,`label4`=?,`content`=?,`cover_img`=?,`quotes`=? WHERE `substitutes_id`=?",[$title,$labelData[0],$labelData[1],$labelData[2],$labelData[3],$content,$coverUrl,$quotes,$substitutes_id]);
        
        echo json_encode(1);
    }

    // 成长历程记录
    public function newGrowth()
    {
         // 接收数据
        $request = Request::instance();
        $content = $request->post( "content" ); 
        $coverUrl = $request->post( "coverUrl" ); 
        $substitutes_id = $request->post( "substitutes_id" ); 
        // // 日期转换为UNIX时间戳
        $time = strtotime( date('Y-m-d H:i') );

        Db::query("INSERT INTO `media_growth`(`substitutes_id`, `time`, `content`, `cover_img`) VALUES (?,?,?,?)",[$substitutes_id,$time,$content,$coverUrl]);

        // 获取买者信息
        $Getgrowthdata = Loader::model('Getgrowthdata');
        $buyerData =  $Getgrowthdata->getBuyer( $substitutes_id );
        // 获取卖者id
        $IndexModel = Loader::model('IndexModel');
        $user_id = $IndexModel->getCookie();
        // // 给买者发送站内通知
        $SendNotice = Loader::model('SendNotice');
        for ($i=0; $i < count($buyerData); $i++) { 
            $SendNotice->sendNotice($user_id,$buyerData[$i]["id"],"您的认养有新的成长历程","尊敬的闲菜网用户，您好！您的认养有新的成长历程了！","4",$substitutes_id);
        }

        echo json_encode(1);
    }

    // 成长历程查看更多
    public function readMoreGrowth()
    {
        // 接收数据
        $request = Request::instance();
        $substitutes_id = $request->post( "substitutes_id" ); 
        $page = $request->post( "page" ); 
        if ($page != 0) {
            // 获取成长历程
            $Getgrowthdata = Loader::model('Getgrowthdata');
            $growth = $Getgrowthdata->growthData(5,$substitutes_id,$page);
        }else {
            $growth = array();
            $growth["nextpage"] = 0;
        }

        echo json_encode( $growth );
    }

    // 查看更多评论
    public function readMoreComent()
    {
        // 接收数据
        $request = Request::instance();
        $substitutes_id = $request->post( "substitutes_id" ); 
        $page = $request->post( "page" ); 
        if ($page != 0) {
            // 获取成长历程
            $Getgrowthdata = Loader::model('Getgrowthdata');
            $growth = $Getgrowthdata->getGrowthComment(6,$substitutes_id,$page);
        }else {
            $growth = array();
            $growth["nextpage"] = 0;
        }

        echo json_encode( $growth );
    }

    // 代养评论发表
    public function writeSubComment()
    {
        // 接收数据
        $request = Request::instance();
        $content = $request->post( "comment" ); 
        $substitutes_id = $request->post( "substitutes_id" ); 
        $qtUserMesgId = $request->post( "qtUserMesgId" ); 

        $IndexModel = Loader::model('IndexModel');
        // 获取用户信息
        $userMesg = $IndexModel->getUseMesg();

        // 日期转换为UNIX时间戳
        $time = strtotime( date('Y-m-d H:i') );

        Db::query("INSERT INTO `media_dynamic`(`type`, `user_id`, `correspond_user_id`, `correspond_id`, `content`, `time`,  `status`) VALUES (?,?,?,?,?,?,?)",[3,$userMesg["id"],$qtUserMesgId,$substitutes_id,$content,$time,0]);

        $time = date('Y-m-d H:i',$time);
        $returnData = array();
        $returnData["userMesg"] = $userMesg;
        $returnData["time"] = $time;
        $returnData["content"] = $content;
        echo json_encode($returnData);
    }

    // 提交订单处理
    public function orderSubmission()
    {
        // 接收数据
        $request = Request::instance();
        $buyer = $request->post( "buyer" );
        $seller = $request->post( "seller" ); 
        $commodityId = $request->post( "commodityId" ); 
        $type = $request->post( "type" ); 

        $IndexModel = Loader::model('IndexModel');
        $sellerMesg = $IndexModel->getIdMesg($seller); 

        // 日期转换为UNIX时间戳
        $time = strtotime( date('Y-m-d H:i') );

        switch ( $type ) {
            case '1':
                # code...
                break;

            // 代养处理
            case '2':
                Db::query( 'INSERT INTO `media_order`(`buyer`, `seller`, `type`, `Corresponding_id`, `time`) VALUES (?,?,?,?,?)',[$buyer,$seller,$type,$commodityId,$time]);
                $order_id = Db::query("SELECT `id` FROM `media_order` WHERE `buyer`=? and `time`=? and `Corresponding_id`=?",[$buyer,$time,$commodityId]);
                $order_id = $order_id[0]["id"];
                break;
        }

        // 发送站内通知
        $SendNotice = Loader::model('SendNotice');
        $SendNotice->sendNotice($buyer,$seller,"您新代养信息申请订单","请先确认您与申请该订单用户意见达成一致，确认后将不可撤销！","2",$order_id);
        
        $time = date('H:i');
        // 发送短信
        $SignName = "闲菜网";
        $TemplateCode = "SMS_95420087";
        $ParamString = "{'nickName':'".$sellerMesg["nick_name"]."','time':'".$time."'}";
        $Singlesendsms = Loader::model('Singlesendsms');
        $mesg = $Singlesendsms->sendMesg($ParamString,$sellerMesg["phone_number"],$SignName,$TemplateCode); 

        echo json_encode(1);
    }

    // 消息处理
    public function noticeHandle()
    {
        // 获取对应操作（1、标记为已读markread，2、刷除delete）
        $operation = $_POST["operation"];
        // 要操作的消息id
        $chooseNotice = $_POST["chooseNotice"]; 
        if ( empty($chooseNotice) ) {
            echo json_encode(0);
            exit(0);
        }

        switch ($operation) {
            case 'markread':
                // 拼接sal字符
                $chooseid = "'".$chooseNotice[0]."'";
                for($i=1; $i< count($chooseNotice);$i++){
                    $chooseid = $chooseid.",'".$chooseNotice[$i]."'";
                }
                $sql = "UPDATE `media_notice` SET `static`=1 WHERE `id` in(".$chooseid.")";
                break;
            
            case 'delete':
                // 拼接sal字符
                $chooseid = "'".$chooseNotice[0]."'";
                for($i=1; $i< count($chooseNotice);$i++){
                    $chooseid = $chooseid.",'".$chooseNotice[$i]."'";
                }
                $sql = "DELETE FROM `media_notice` WHERE `id` in(".$chooseid.")";
                break;
        }

        Db::query($sql);
        
        echo json_encode( 1 );
    }

    // 获取某条消息具体信息
    public function getSpecificNotice()
    {
        $request = Request::instance();
        $id = $request->post( "id" );

        $SendNotice = Loader::model('SendNotice');
        echo json_encode( $SendNotice->getOneDetailed($id) );
    }
    // 授权用户订单申请
    public function authorizeOrder()
    {
        $request = Request::instance();
        $order_id = $request->post( "order_id" );

        Db::query("UPDATE `media_order` SET `static`=? WHERE `id`=?",[1,$order_id]);
        echo json_encode(1);
    }

    // 注册时检验基本信息和验证码是否正确
    public function receiveData()
    {
        // 较验手机号、昵称是否存在，和验证码是否正确;
        $Register = Loader::model('Register');
        echo json_encode( $Register->receiveData() );
    }
    // 注册时较验手机是否能收到短信
    public function sendCaptchaToPhone()
    {
        $Register = Loader::model('Register');
        echo json_encode( $Register->sendCaptchaToPhone() );
    }
    // 注册
    public function register()
    {
        $Register = Loader::model('Register');
        echo json_encode( $Register->register() );
    }

    // 申请成为供应商
    public function saveApply()
    {
        //获取数据
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $userMesg = $IndexModel->getUseMesg();
        $user_id = $IndexModel->getCookie();
        if ( empty($userMesg) ) {
            echo -1;                            //没有登录
            exit(0);
        }
        $request = Request::instance();
        $address = $request->post( "address" );
        $birthday = $request->post( "birthday" );
        $gender = $request->post( "gender" );
        $idcard = $request->post( "idcard" );
        $name = $request->post( "name" );
        $race = $request->post( "race" );
        
        Db::query("UPDATE `media_user` SET `name`=?,`address`=?,`birthday`=?,`gender`=?,`id_card_number`=?,`race`=?, `supplier`=? WHERE `id`=?",[$name,$address,$birthday,$gender,$idcard,$race,1,$user_id]);
        Db::query("INSERT INTO `media_supplier`(`id`) VALUES (?)",[$user_id]);
        echo json_encode( 1 );
    }

    // 人脸授权
    public function faceAuthorization()
    {
        // 获取用户信息
        $IndexModel = Loader::model('IndexModel');
        $user_id = $IndexModel->getCookie();
        if ( empty($user_id) ) {
            echo "授权失败";                            //没有登录
            exit(0);
        }
        // 获取post数据
        $request = Request::instance();
        $face_token = $request->post("face_token");         // face_token

        // 操作
        Db::query("UPDATE `media_user` SET `face_token`=? WHERE `id`=?",[$face_token,$user_id]);
        $data = Db::query("SELECT `id`, `name`, `password` FROM `media_user` WHERE `face_token`=?",[$face_token]);

        if (empty($data)) {
            echo "注册失败！";
            exit(0);
        }
        echo 1;
    }
}