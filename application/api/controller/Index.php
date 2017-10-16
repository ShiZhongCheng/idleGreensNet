<?php
namespace app\api\controller;
use think\Db;
use think\Request;

class Index
{
    public function index()
    {
        dump(Db::query('SELECT * FROM `phy_4_usehistory`'));
    }

    // 获取用户信息
    public function getMyMesg()
    {
    	//获取openid参数
        $request = Request::instance();
        $openid = $request->get("openid");

        $myMesg = Db::query("SELECT `id`, `name`, `studentID`, `nickname`, `headImageUrl`, `phonenumber`, `role`, `class_number` FROM `physical_student` WHERE `oppenid`=?",[$openid]);

        // 返回给接口
        echo json_encode($myMesg);
    }

    //获取openid
    public function getOpenid()
    {
     	//获取code
     	$request = Request::instance();
        $code = $request->get("code");

     	//获取小程序配置信息
        $appid = config('appid');
        $appsecret = config('appsecret');

        if(isset($code)){
			$url='https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$appsecret.'&js_code='.$code.'&grant_type=authorization_code';
		    $curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL, $url);
		    curl_setopt($curl, CURLOPT_HEADER, 0);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		    $data = curl_exec($curl);
		    curl_close($curl);

		    echo $data;
		}
     }

    //获取对应编号使用情况
    public function getUseMesg()
    {
    	//获取yqid
     	$request = Request::instance();
        $yqid = $request->get("yqid");

        if(isset($yqid)){
        	// 字符串处理
		    $phy_id=substr($yqid, 0,2); 
		    $phy_id = $phy_id+1-1;
			$bianhao=substr($yqid, 2,2);
			
			// 查询对应phy_id实验信息
			$rs = Db::query("SELECT * FROM `physical_name` WHERE `phy_id`=?",[$phy_id]);
		    if ($rs[0]["useing"]==0) {
				echo 0;
				exit(0);
		    }

			//获得对应编号使用情况
			$sql2db="phy_".$phy_id;
			$rs2 = Db::query('SELECT * FROM `'.$sql2db.'` WHERE bianhao=? ',[$bianhao]);
			$sum=$rs2[0]["sum"];
			
			$data = array('rs' => $rs,'bianhao'=>$bianhao,'sum'=>$sum );

			echo json_encode($data);
		}
    }

    //实验记录
    public function recondMesg()
    {
    	//获取数据
     	$request = Request::instance();
        $openid = $request->get("openid");
		$bianhao = $request->get("bianhao");
		$ex_id = $request->get("ex_id");
		$name = $request->get("name");
		$classroomnumber = $request->get("classroomnumber");
		$sum = $request->get("sum");
		$phy_name = $request->get("phy_name");
		$useteacher = $request->get("useteacher");
		$fId = $request->get("fId");

		// 对phy_id进行处理
		$phy_id=substr($_GET["yqid"], 0,2); 
		$phy_id = $phy_id+1-1;

		$loc = "phy_".$phy_id."_usehistory";  //拼接数据表
		$start_time = date('Y/m/d H:i:s');   //开始时间

		// phy_x_usehistory表登记
		// $sql = 'INSERT INTO `'.$loc.'`(`name`, `oppenid`, `classroomnumber`,`teachername`, `phy_name`,`bianhao`,`flag`,`ex_id`, `start_time`, `end_time`) VALUES (?,?,?,?,?,?,"0","0")';
		$query2 = Db::query('INSERT INTO `'.$loc.'`(`name`, `oppenid`, `classroomnumber`,`teachername`, `phy_name`,`bianhao`, `ex_id`, `start_time`, `flag`, `end_time`) VALUES (?,?,?,?,?,?,?,?,"0","0")',[$name,$openid,$classroomnumber,$useteacher,$phy_name ,$bianhao,$ex_id,$start_time]);

		// 修改sum +1
		$loc3 = "phy_".$phy_id;
		$newsum = $sum+1;
		$query3 = Db::query('UPDATE `'.$loc3.'` SET `sum`='.$newsum.' WHERE `bianhao`='.$bianhao);

		echo $start_time;
		exit(1);
		// dump($request->get());
    }
    // 发送模板信息函数
    public function sendMesg($a)
    {
    	echo $a;
    }

    // 报修
    public function baoXiu()
    {
    	//获取数据
     	$request = Request::instance();
        $openid = $request->get("openid");
	    $message = $request->get("message");
	    $name = $request->get("name");
	    $phonenumber = $request->get("phonenumber");
	    $yqid = $request->get("yqid");

	    $time = date('Y/m/d H:i:s');

	    Db::query('INSERT INTO `physical_baoxiu`(`openid`, `name`, `phonenumber`, `message`, `yqid`, `time`, `flag`) VALUES (?,?,?,?,?,?,"0")',[$openid,$name,$phonenumber,$message,$yqid,$time]);

	    echo 1;
    }

    //学生信息录入
    public function studentInput()
    {
    	//获取数据
     	$request = Request::instance();
        $openid = $request->get("openid");
        $studentId = $request->get("studentId");
	    $name = $request->get("name");
	    $phonenumber = $request->get("phonenumber");
        $nickname = $request->get("nickname");
        $headImageUrl = $request->get("headImageUrl");

        $ifExit = Db::query("SELECT `id` FROM `physical_student` WHERE `oppenid`=?",[$openid]);

        if ( !empty($ifExit) ) {
            echo 0;
            exit();
        }

        if ( !empty($openid) && !empty($studentId) && !empty($name) && !empty($phonenumber) && !empty($nickname) && !empty($headImageUrl) ) {
            Db::query('INSERT INTO `physical_student`(`name`, `studentID`, `oppenid`, `phonenumber`, `nickname`, `headImageUrl`) VALUES (?, ?, ?, ?, ?, ?)',[$name,$studentId,$openid,$phonenumber,$nickname,$headImageUrl]);
            echo 1;
            exit();
        }
        echo 0;
    }

    // 学生信息修改
    public function studentMesgChange()
    {
    	//获取数据
     	$request = Request::instance();
     	$value = $request->get("value");
     	$ctype = $request->get("ctype");
        $openid = $request->get("openid");

     	Db::query('UPDATE `physical_student` SET `'.$ctype.'`=? WHERE `oppenid`=?',[$value,$openid]);
     	echo 1;
    }

    //学生实验记录
    public function showMyEx()
    {
    	//获取数据
     	$request = Request::instance();
        $openid = $request->get("openid");

        $arrayName = array();

        $phy_id = Db::query('SELECT `phy_id` FROM `physical_name` WHERE 1');
        $count =  count($phy_id);
        for (; $count >= 1; $count--) { 
        	$id = $phy_id[$count-1]["phy_id"];
			$phy_x_usehistory = "phy_".$id."_usehistory";

			$arr = Db::query('SELECT * FROM `'.$phy_x_usehistory.'` WHERE `oppenid`=?',[$openid]);
			$arrayName = array_merge_recursive($arrayName, $arr);
        }
        echo json_encode($arrayName);
    }

    // 学生呼叫老师
    public function askTeacherHelp()
    {
        //获取数据
        $request = Request::instance();
        $yqid = $request->get("yqid");
        // 字符串处理
        $phy_id=substr($yqid, 0,2); 
        $phy_id = $phy_id+1-1;
        $bianhao=substr($yqid, 2,2);

        // 判断实验是否在进行
        $useingArr = Db::query('SELECT `useing` FROM `physical_name` WHERE `phy_id`=?',[$phy_id]);
        if (!empty($useingArr)) {
            $useing = $useingArr[0]["useing"];
        }else{
            echo 0;
            exit(1);
        }
        if ($useing == 0) {
            echo 0;
            exit(1);
        }
        Db::query('INSERT INTO `askteacherhelp`(`yqid`,`phy_id`,`bianhao`,`flag`) VALUES (?,?,?,0)',[$yqid,$phy_id,$bianhao]);
        echo 1;
    }

    // 发布动态
    public function publishDynamic()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $title = $request->get("title");
        $content = $request->get("content");
        $announcer = $request->get("announcer");
        $img = $request->get("img");
        $status = 1;

        $time = date("Y/m/d");

        Db::query("INSERT INTO `phy_development`(`title`, `content`, `time`, `announcer`, `img`, `status`) VALUES (?,?,?,?,?,?)",[$title,$content,$time,$announcer,$img,$status]);
        echo 1;
    }

    // 修改和添加我的班级号
    public function addMyClassNumber()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $class_number = $request->get("class_number");
        $openid = $request->get("openid");
        Db::query("UPDATE `physical_student` SET `class_number`=? WHERE `oppenid`=?",[$class_number,$openid]);
        $myMesg = Db::query("SELECT `id`, `name`, `studentID`, `headImageUrl`, `phonenumber`, `role`, `class_number` FROM `physical_student` WHERE `oppenid`=?",[$openid]);

        // 返回给接口
        echo json_encode($myMesg);
    }

    // 显示所有课程
    public function getAllEx()
    {
        $data = Db::query("SELECT * FROM `physical_name` WHERE 1");
        echo json_encode( $data );
    }

    // 获取弹幕信息
    public function getDanMu()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $phy_id = $request->get("phy_id");
        $initial_data = Db::query("SELECT `text`, `color`, `time` FROM `phy_damu` WHERE `static`=? and `phy_id`=?",[1,$phy_id]);
        echo json_encode( $initial_data );
    }

    // 发弹幕
    public function saveDanMu()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $phy_id = $request->get("phy_id");
        $text = $request->get("text");
        $color = $request->get("color");
        $time = $request->get("time");
        $announcer = $request->get("announcer");
        Db::query("INSERT INTO `phy_damu`(`phy_id`, `text`, `color`, `time`, `static`, `announcer`) VALUES (?,?,?,?,?,?)",[$phy_id,$text,$color,$time,1,$announcer]);
        echo 1;
    }

    // 获取视频长度
    public function getMovieLeng()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $phy_id = $request->get("phy_id");
        $data = Db::query("SELECT * FROM `physical_name` WHERE `phy_id`=?",[$phy_id]);
        echo json_encode( $data[0] );
    }

    // 看视频时间达到要求
    public function achieveTime()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $phy_id = $request->get("phy_id");
        $openid = $request->get("openid");
        $oldData = Db::query("SELECT `finished_video` FROM `physical_student` WHERE `oppenid`=?",[$openid]);
        if ($oldData[0]["finished_video"] == "") {
            $write_data = $phy_id . ",";
        }else{
            // 分割字符串成数组
            $segmentation = explode(',',$oldData[0]["finished_video"] ); 
            for($index=0;$index < count($segmentation) ;$index++) 
            { 
                if ($segmentation[$index] == $phy_id) {
                    echo 2;
                    exit();
                }
            } 
            $phy = $phy_id . ',';
            $write_data =  $oldData[0]["finished_video"] . $phy;
        }
        Db::query("UPDATE `physical_student` SET `finished_video`=? WHERE `oppenid`=?",[$write_data,$openid]);
        echo 1;
    }

    // 获取已观视频目录
    public function getFinishedVideo()
    {
        //获取数据
        $request = Request::instance();
        if ( empty( $_GET ) ) {
            echo 0;
            exit();
        }
        $openid = $request->get("openid");
        $oldData = Db::query("SELECT `finished_video` FROM `physical_student` WHERE `oppenid`=?",[$openid]);
        if ($oldData[0]["finished_video"] == "") {
            $return_data = "";
        }else{
            // 分割字符串成数组
            $return_data = explode(',',$oldData[0]["finished_video"] ); 
        }
        echo json_encode( $return_data );
    }
    
    // 图片上传
    public function subshow_upload()
    {
        $request = Request::instance();
        $userID = $request->post("userID");
        if ( empty($userID) ) {
            echo "no userId!";
            exit();
        }

        $file = request()->file('imgFile');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext'=>'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'phy');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $fileRoot = 'https://www.goooooo.top/phy/'.str_replace('\\','/',$info->getSaveName());

            // 图片上传成功，更爱=改我的头像
            Db::query("UPDATE `physical_student` SET `headImageUrl`=? WHERE `id`=?",[$fileRoot,$userID]);
            echo $fileRoot;
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
}