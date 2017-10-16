<?php
namespace app\api\controller;
use think\View;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Myfunction
{
	// 登录记住cookie
	public function loinSave()
	{
		$request = Request::instance();
		$zhanghao = $request->post("zhanghao");
		$password = $request->post("password");
		// 对密码进行MD5编码
		$password = md5($password);
		$remberPassword = $request->post("remberPassword");

		// 判断账号和密码是否正确
		$userMesg = Db::query("SELECT `headImageUrl`, `zhanghao` FROM `physical_teacher` WHERE `zhanghao`=? and `password`=?",[$zhanghao,$password]);
		if ( empty($userMesg) ) {
			echo json_encode("not");
        	exit();
		}

		switch ($remberPassword) {
			case 'true':
				$saveTime = 604800;
				break;
			case 'false':
				$saveTime = "";
				break;
		}
		// cookie初始化
        Cookie::init(['prefix'=>'user_','expire'=>86400,'path'=>'/']);
        // 删除指定cookie
        Cookie::delete('zhanghao');
        Cookie::delete('password');

        // 保存cookie
        Cookie::set('zhanghao',$zhanghao);
        Cookie::set('password',$password);

        // 判断cookie是否设置成功
        if ( !Cookie::has('zhanghao','user_') && !Cookie::has('password','user_') ) {
        	// 设置失败
        	echo json_encode("fail");
        	exit();
        }
        // 设置成功
        echo json_encode("success");
	}

	// 退出，清除cookie
	public function dropOut()
	{
		// 删除指定cookie
        Cookie::delete('user_zhanghao');
        Cookie::delete('user_password');
        
        // 判断是否需要登录
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifNeedLogin();
	}

	// 小程序图片设置修改
	public function carouselMesgChange()
	{
		$request = Request::instance();
		if ( empty($request->post()) ) {
			echo json_encode("fail");
			exit();
		}
		$parameter = $request->post("parameter");
		$type = $request->post("type");
		$newValue = $request->post("newValue");

		switch ($type) {
			// 说明修改
			case 'configure-explain':
				Db::query("UPDATE `phy_configure` SET `configure_explain`=? WHERE `id`=?",[$newValue,$parameter]);
				echo json_encode("conf-ex-success");
				break;

			// 轮播图修改
			case 'configure-img':
				Db::query("UPDATE `phy_configure` SET `value`=? WHERE `id`=?",[$newValue,$parameter]);
				echo json_encode("conf-im-success");
				break;
		}
	}

	// 课程内容更改
	public function courseMesgChange()
	{
		$request = Request::instance();
		if ( empty($request->post()) ) {
			echo json_encode("fail");
			exit();
		}
		$parameter = $request->post("parameter");
		$type = $request->post("type");
		$newValue = $request->post("newValue");

		switch ($type) {
			// 修改实验名称
			case 'configure-ex-name':
				Db::query("UPDATE `physical_name` SET `phy_name`=? WHERE `phy_id`=?",[$newValue,$parameter]);
				echo json_encode("conf-ex-name-success");
				break;

			// 课程题图修改
			case 'configure-img':
				Db::query("UPDATE `physical_name` SET `img_url`=? WHERE `phy_id`=?",[$newValue,$parameter]);
				echo json_encode("conf-im-success");
				break;

			// 课程实验教室修改
			case 'configure-class-room':
				Db::query("UPDATE `physical_name` SET `classroomnumber`=? WHERE `phy_id`=?",[$newValue,$parameter]);
				echo json_encode("conf-room-success");
				break;

			// 课程实验视频说明
			case 'configure-video-explan':
				Db::query("UPDATE `physical_name` SET `explain_people`=? WHERE `phy_id`=?",[$newValue,$parameter]);
				echo json_encode("conf-video-explan-success");
				break;

			// 课程实验视频时长
			case 'configure-video-leng':
				Db::query("UPDATE `physical_name` SET `phy_movie_leng`=? WHERE `phy_id`=?",[$newValue,$parameter]);
				echo json_encode("conf-video-leng-success");
				break;

			// 实验视频上传
			case 'configure-video-upload':
				Db::query("UPDATE `physical_name` SET `video_url`=? WHERE `phy_id`=?",[$newValue,$parameter]);
				echo json_encode("conf-video-upload-success");
				break;
		}
	}

	// 修改用户身份
	public function roleChange()
	{
		$request = Request::instance();
		if ( empty($request->post()) ) {
			echo json_encode("fail");
			exit();
		}
		$openid = $request->post("openid");
		$role = $request->post("role");

		switch ($role) {
			// 设为管理员
			case 1:
				// 先改表physical_student的角色
				Db::query("UPDATE `physical_student` SET `role`=? WHERE `oppenid`=?",[$role,$openid]);
				// 判断表physical_teacher中是否存在
				$ifExit = Db::query("SELECT `id` FROM `physical_teacher` WHERE `oppenid`=?",[$openid]);
				if ( empty($ifExit) ) {
					$userMesg = Db::query("SELECT * FROM `physical_student` WHERE `oppenid`=?",[$openid]);
					$userMesg = $userMesg[0];
					Db::query("INSERT INTO `physical_teacher`(`name`, `oppenid`, `headImageUrl`, `zhanghao`, `password`, `role`) VALUES (?,?,?,?,?,?)",[$userMesg["name"],$openid,$userMesg["headImageUrl"],$userMesg["studentID"],md5($userMesg["studentID"]),1]);
				}else{
					Db::query("UPDATE `physical_teacher` SET `role`=? WHERE `oppenid`=?",[1,$openid]);
				}
				break;

		    // 设置为教师
			case 2:
				// 先改表physical_student的角色
				Db::query("UPDATE `physical_student` SET `role`=? WHERE `oppenid`=?",[$role,$openid]);
				// 判断表physical_teacher中是否存在
				$ifExit = Db::query("SELECT `id` FROM `physical_teacher` WHERE `oppenid`=?",[$openid]);
				if ( empty($ifExit) ) {
					$userMesg = Db::query("SELECT * FROM `physical_student` WHERE `oppenid`=?",[$openid]);
					$userMesg = $userMesg[0];
					Db::query("INSERT INTO `physical_teacher`(`name`, `oppenid`, `headImageUrl`, `zhanghao`, `password`, `role`) VALUES (?,?,?,?,?,?)",[$userMesg["name"],$openid,$userMesg["headImageUrl"],$userMesg["studentID"],md5($userMesg["studentID"]),0]);
				}else{
					Db::query("UPDATE `physical_teacher` SET `role`=? WHERE `oppenid`=?",[0,$openid]);
				}
				break;
			
			default:
				Db::query("UPDATE `physical_student` SET `role`=? WHERE `oppenid`=?",[$role,$openid]);
				break;
		}

		echo json_encode("success");
	}

	// ajax长轮询
	public function polling()
	{
		if(empty($_POST['time'])) exit();    
		$ex_id = $_POST['ex_id'];
		$phy_id = $_POST['phy_id'];
		set_time_limit(0);//无限请求超时时间     
		$i=0;     
		while (true)
		{     
		    //sleep(1);     
		    usleep(500000);//0.5秒     
		    $i++;     
		           
		    //若得到数据则马上返回数据给客服端，并结束本次请求     
		    $userData = Db::query("SELECT `id`, `name`, `oppenid`, `bianhao`, `ex_id`, `end_time` FROM `phy_".$phy_id."_usehistory` WHERE `ex_id`=? and `flag`=?",[$ex_id,0]); 
		    $helpData = Db::query("SELECT `id`, `bianhao` FROM `askteacherhelp` WHERE `phy_id`=? AND `flag`=?",[$phy_id,0]);
		    if( !empty($userData) ){  
			    foreach($userData as $val) {
				   Db::query("UPDATE `phy_".$phy_id."_usehistory` SET `flag`=? WHERE `id`=?",[1,$val["id"]]);
				}   
		        $arr=array('success'=>"1",'data'=>$userData);     
		        echo json_encode($arr);     
		        exit();     
		    }   

		    if( !empty($helpData) ){  
			    foreach($helpData as $val) {
				   Db::query("UPDATE `askteacherhelp` SET `flag`=? WHERE `id`=?",[1,$val["id"]]);
				}   
		        $arr=array('success'=>"2",'data'=>$helpData);     
		        echo json_encode($arr);     
		        exit();     
		    }  
		           
		    //服务器($_POST['time']*0.5)秒后告诉客服端无数据     
		    if($i==$_POST['time']){     
		        $arr=array('success'=>"0");     
		        echo json_encode($arr);     
		        exit();     
		    }     
		}
	}

	// 
	public function userEnd()
	{
		$request = Request::instance();
		if ( empty($request->post()) ) {
			$arr=array('success'=>"0");  
			exit();
		}
		$id = $request->post("id");
		$phy_id = $request->post("phy_id");

		$end_time = date("Y/m/d H:i:s");

		Db::query("UPDATE `phy_".$phy_id."_usehistory` SET `end_time`=? WHERE `id`=?",[$end_time,$id]);
		$arr=array('success'=>"1");     
        echo json_encode($arr);     
	}

	// 导出excle
	public function exportExcle()
	{
		//获取数据
        $request = Request::instance();
        if (empty($request->get())){
           // 跳转到实验记录页面
           header("Location: ".$_SERVER['SCRIPT_NAME']."/api/admin/experimentalRecord");
           exit();
        }
        $ex_id = $request->get("ex_id");
        $phy_id = $request->get("phy_id");

        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$ex_id.".xls");

        //输出内容如下：
        echo   "序号"."\t";  
        echo   "姓名"."\t"; 
        echo   "学号"."\t";
        echo   "编号"."\t"; 
        echo   "开始时间"."\t"; 
        echo   "结束时间"."\t"; 
        echo   "总共用时(分钟)"."\t";
        echo   "观看了视频"."\t"; 
        echo   "\n"; 

        // 拼接数据表
        $loc="phy_".$phy_id."_usehistory";
        $data1 = Db::query('SELECT `name`, `oppenid`, `bianhao`, `start_time`, `end_time` FROM `'.$loc.'` WHERE `ex_id`=?',[$ex_id]);
        $long = count($data1);
        for ($i=0; $long >= 1; $long--) { 
        	$data2 = Db::query('SELECT `studentID`,`finished_video` FROM `physical_student` WHERE `oppenid`=?',[$data1[$i]["oppenid"]]);
        	// 获取学生学号
            $data1[$i]["studentID"] = $data2[0]["studentID"];
            // 获取学生是否观看视频
            $finshedVideoArry = explode(",", $data2[0]["finished_video"]); 
            $data1[$i]["haveWatchVideo"] = "未完成视频预习";
            for ($k=0; $k < count($finshedVideoArry); $k++) { 
            	if ($finshedVideoArry[$k] == $phy_id) {
            		$data1[$i]["haveWatchVideo"] = "已完成视频预习";
            	}
            }
            // 计算实验用时
            // 用时
            if ($data1[$i]["end_time"] == '0') {
            	$data1[$i]["allUseTime"] = "此学生实验未结束";
            }else{
                $sh = $data1[$i]["start_time"][11].$data1[$i]["start_time"][12];
                $sm = $data1[$i]["start_time"][14].$data1[$i]["start_time"][15];

                $eh = $data1[$i]["end_time"][11].$data1[$i]["end_time"][12];
                $em = $data1[$i]["end_time"][14].$data1[$i]["end_time"][15];
                $data1[$i]["allUseTime"] = floor( ($eh-$sh)*60 + ($em-$sm) );
                if ($data1[$i]["allUseTime"]<=0) {
                   $data1[$i]["allUseTime"] = '时间错误';
                }
                if ($data1[$i]["allUseTime"] > 1440) {
                    $data1[$i]["allUseTime"] = '用时超过24小时';
                }
                // dump($data1[$long-1]);
                // echo "sh=".$sh."sm=".$sm."eh=".$eh."em=".$em."\n";
                // echo $allUseTime;  
            }
            // 输出
            $num = $i + 1;
            echo   $num."\t"; 
            echo   $data1[$i]["name"]."\t"; 
            echo   $data1[$i]["studentID"]."\t"; 
            echo   $data1[$i]["bianhao"]."\t"; 
            echo   $data1[$i]["start_time"]."\t";
            echo   $data1[$i]["end_time"]."\t";
            echo   $data1[$i]["allUseTime"]."\t";
            echo   $data1[$i]["haveWatchVideo"]."\t"; 
            echo   "\n"; 
            $i++;
        }
	}

	// 结束实验
	public function endEx()
	{
		//获取数据
        $request = Request::instance();
        if (empty($request->get())){
           // 跳转到实验记录页面
           header("Location: ".$_SERVER['SCRIPT_NAME']."/api/admin/experimentalRecord");
           exit();
        }
        $ex_id = $request->get("ex_id");
        $phy_id = $request->get("phy_id");

        $endTime = date("Y/m/d H:i:s"); 

        Db::query("UPDATE `phy_".$phy_id."_usehistory` SET `end_time`=? WHERE `ex_id`=? and `end_time`=?",[$endTime,$ex_id,0]);
        Db::query("UPDATE `teacherusehistory` SET `isend`=? WHERE `ex_id`=?",[1,$ex_id]);
        Db::query("UPDATE `physical_name` SET `Description`='',`useing`=?,`useteacher`='',`limite`=?,`ex_id`='' WHERE `phy_id`=?",[0,0,$phy_id]);
        Db::query("UPDATE `phy_".$phy_id."` SET `sum`=0 WHERE 1");
        header("Location: ".$_SERVER['SCRIPT_NAME']."/api/admin/experimentalRecord");
        exit();
	}
}