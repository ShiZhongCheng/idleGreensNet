<?php
namespace app\api\controller;
use think\Db;
use think\Request;

class Teacher
{
	public function index()
    {
        dump(Db::query('SELECT * FROM `phy_4_usehistory`'));
    }

    // 教师教室使用记录
    public function classRoomUseHis()
    {
    	//获取数据
     	$request = Request::instance();
        $openid = $request->get("openid");

        $data = Db::query('SELECT * FROM `teacherusehistory` WHERE `oppenid`=?',[$openid]);
        echo json_encode($data);
    }

    // 使用教室处理
    public function classRoomUse()
    {
    	//获取数据
     	$request = Request::instance();
     	$useteacher = $request->get("useteacher");
     	$phy_name = $request->get("phy_name");
     	$ex_id = $request->get("ex_id");
     	$limite = $request->get("limite");
     	$Description = $request->get("Description");
     	$phy_id = $request->get("phy_id");
     	$openid = $request->get("openid");

     	// 记录physical_name
     	Db::query('UPDATE `physical_name` SET `Description`=?,`useing`="1",`useteacher`=?,`limite`=?,`ex_id`=? WHERE `phy_id`=?',[$Description,$useteacher,$limite,$ex_id,$phy_id]);
     	// 查询
     	$data = Db::query('SELECT `useing`,`classroomnumber` FROM `physical_name` WHERE `phy_id`=?',[$phy_id]);

     	$classroomnumber = $data[0]["classroomnumber"];

     	// useTecherHistory记录
     	DB::query('INSERT INTO `teacherusehistory`(`teachername`, `oppenid`, `classroomnumber`, `ex_id`, `limite`, `phy_id`, `phy_name`, `isend`) VALUES (?,?,?,?,?,?,?,"0")',[$useteacher,$openid,$classroomnumber,$ex_id,$limite,$phy_id,$phy_name]);

     	// 查询useTecherHistory
     	$data2 = DB::query('SELECT `isend` FROM `teacherusehistory` WHERE `ex_id`=?',[$ex_id]);

     	if ($data[0]["useing"] == 1 && $data2[0]["ex_id"] == 0) {
     		echo 1;
     	}else{
 			echo 0;
     	}
    }

    // 获取实验列表
    public function exList()
    {
    	$data = Db::query('SELECT * FROM `physical_name` WHERE `useing`="0"');
        echo json_encode($data);
    }

    // 获取报修记录
    public function getBaoXiuHis()
    {
    	$data = Db::query('SELECT * FROM `physical_baoxiu` WHERE `flag`="0"');
    	echo json_encode($data);
    }

    // 确认报修
    public function sureBaoXiu()
    {
    	//获取数据
     	$request = Request::instance();
     	$id = $request->get("id");

     	Db::query('UPDATE `physical_baoxiu` SET flag="1" WHERE `id`=?',[$id]);
     	$data = Db::query('SELECT `flag` FROM `physical_baoxiu` WHERE `id`=?',[$id]);
     	if ($data[0]["flag"]==1) {
     		echo 1;
     	}else{
     		echo 0;
     	}
    }

    // 新建实验
    public function creatNewEx()
    {
    	//获取数据
     	$request = Request::instance();
     	$phy_name = $request->get("phy_name");
     	$classroomnumber = $request->get("classroomnumber");

     	if ($phy_name=='' || $classroomnumber=='') {
     		echo -1;
     		exit(0);
     	}

     	// 查看是否有同名实验
     	$id = Db::query('SELECT `phy_id` FROM `physical_name` WHERE `phy_name`=?',[$phy_name]);
     	if (!empty($id)) {
     		echo 0;
     		exit(0);
     	}

     	// 向physical_name中插入数据
     	Db::query("INSERT INTO `physical_name` (`phy_name`, `classroomnumber`, `useing`, `useteacher`, `limite`, `ex_id`) VALUES (?,?,'0','','0','')",[$phy_name,$classroomnumber]);

     	// 获取phy_id
     	$phy = Db::query('SELECT `phy_id` FROM `physical_name` WHERE `phy_name`=?',[$phy_name]);
     	if (empty($phy)) {
	    	echo 1;
	    	exit(1);
	    }
     	$phy_id = $phy[0]["phy_id"];

     	// 创建phy_x和phy_x_usehistory数据表

    	// 表phy_x
    	Db::query("create table phy_".$phy_id." (
			id int unsigned not null auto_increment primary key,
			bianhao varchar(5),
			sum int(3))
			DEFAULT CHARACTER SET=utf8");

    	// 给表phy_x创建30个值
        $sql3 = "INSERT INTO `phy_".$phy_id."` (`bianhao`,`sum`) VALUES ('01','0')";
    	for ($i=2; $i <=50 ; $i++) { 
    		$n = sprintf('%02d',$i);
    		$sql3 .= ",('$n','0')";
    	}
    	Db::query($sql3);

    	// 表phy_x_usehistory
   //  	Db::query("create table phy_".$phy_id."_usehistory (
			// id int unsigned not null auto_increment primary key,
			// name varchar(20),
			// oppenid varchar(30),
			// classroomnumber varchar(5),
			// teachername varchar(20),
			// phy_name varchar(100),
			// bianhao varchar(5),
			// ex_id varchar(30),
			// start_time varchar(30),
			// end_time varchar(30)),
			// flag int(2)
			// DEFAULT CHARACTER SET=utf8");
        Db::query("create table phy_".$phy_id."_usehistory (
                id int unsigned not null auto_increment primary key,
                name varchar(20),
                oppenid varchar(30),
                classroomnumber varchar(5),
                teachername varchar(20),
                phy_name varchar(100),
                bianhao varchar(5),
                ex_id varchar(30),
                start_time varchar(30),
                flag tinyint(2),
                end_time varchar(30))
                DEFAULT CHARACTER SET=utf8");
    	
    	echo 2;
    }

    // 统计
    public function statistics()
    {
        //获取数据
        $request = Request::instance();
        $phy_name = $request->get("phy_name");
        $ex_id = $request->get("ex_id");
        $phy_id = $request->get("phy_id");
        if (empty($request->get())){
           echo 0;
           exit(1);
        }

        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$phy_name.".xls");
        //输出内容如下：
        echo   "序号"."\t";  
        echo   "姓名"."\t"; 
        echo   "学号"."\t";
        echo   "编号"."\t"; 
        echo   "开始时间"."\t"; 
        echo   "结束时间"."\t"; 
        echo   "总共用时(分钟)"."\t"; 
        echo   "\n"; 

        // 拼接数据表
        $loc="phy_".$phy_id."_usehistory";
        $data1 = Db::query('SELECT `name`, `oppenid`, `bianhao`, `start_time`, `end_time` FROM `'.$loc.'` WHERE `ex_id`=?',[$ex_id]);
        $long = count($data1);
        for ($i=0; $long >= 1; $long--) { 
            $i++;
            $data2 = Db::query('SELECT `studentID` FROM `physical_student` WHERE `oppenid`=?',[$data1[$long-1]["oppenid"]]);
            // 学号
            $studentId = $data2[0]["studentID"];
            // 用时
            if ($data1[$long-1]["end_time"] == '0') {
                $allUseTime = 0;
            }else{
                $sh = $data1[$long-1]["start_time"][11].$data1[$long-1]["start_time"][12];
                $sm = $data1[$long-1]["start_time"][14].$data1[$long-1]["start_time"][15];

                $eh = $data1[$long-1]["end_time"][11].$data1[$long-1]["end_time"][12];
                $em = $data1[$long-1]["end_time"][14].$data1[$long-1]["end_time"][15];
                $allUseTime = ($eh-$sh)*60 + ($em-$sm);
                if ($allUseTime<=0) {
                   $allUseTime = 0;
                }
                if ($allUseTime > 1440) {
                    $allUseTime = 0;
                }
                // dump($data1[$long-1]);
                // echo "sh=".$sh."sm=".$sm."eh=".$eh."em=".$em."\n";
                // echo $allUseTime;  
            }
            // 输出
            echo   $i."\t"; 
            echo   $data1[$long-1]["name"]."\t"; 
            echo   $studentId."\t"; 
            echo   $data1[$long-1]["bianhao"]."\t"; 
            echo   $data1[$long-1]["start_time"]."\t";
            echo   $data1[$long-1]["end_time"]."\t";
            echo   $allUseTime."\t";
            echo   "\n"; 
        }
    }

    // 结束实验
    public function endEx()
    {
        //获取数据
        $request = Request::instance();
        $ex_id = $request->get("ex_id");
        $phy_id = $request->get("phy_id");
        if (empty($request->get())){
           echo 0;
           exit(1);
        }

        // 修改physical_name
        Db::query('UPDATE `physical_name` SET `Description`="",`useing`="0",`useteacher`="",`limite`="0",`ex_id`="" WHERE `phy_id`=?',[$phy_id]);
        // 修改phy_x_usehistory
        $loc="phy_".$phy_id."_usehistory";
        $end_time = date('Y/m/d H:i:s');
        Db::query('UPDATE `'.$loc.'` SET `end_time`=? WHERE `ex_id`=?',[$end_time,$ex_id]);

        // 修改teacherusehistory
        Db::query('UPDATE `teacherusehistory` SET `isend`="1" WHERE `ex_id`=?',[$ex_id]);

        // 修改phy_x
        $locc ="phy_".$phy_id;
        Db::query('UPDATE `'.$locc.'` SET `sum`="0" WHERE 1');

        echo 1;
    }

    // 实时数据
    public function shishi()
    {
        //获取数据
        $request = Request::instance();
        $ex_id = $request->get("ex_id");
        $phy_id = $request->get("phy_id");
        if (empty($request->get())){
           echo 0;
           exit(1);
        }

        $loc="phy_".$phy_id."_usehistory";
        $data = Db::query('SELECT `name`, `oppenid`, `bianhao`, `start_time`, `end_time`, `flag` FROM `'.$loc.'` WHERE `ex_id`=?',[$ex_id]);
        echo json_encode($data);
    }

    // 为学生结束时间
    public function endPeople()
    {
        //获取数据
        $request = Request::instance();
        $ex_id = $request->get("ex_id");
        $phy_id = $request->get("phy_id");
        $openid = $request->get("openid");

        $loc="phy_".$phy_id."_usehistory";
        $end_time = date('Y/m/d H:i:s');
        Db::query('UPDATE `'.$loc.'` SET `end_time`=? WHERE `oppenid`=? and `ex_id`=?',[$end_time,$openid,$ex_id]);
        echo $end_time;
    }

    // 获取askTeacherhelp数据
    public function getAskHelp()
    {
        //获取数据
        $request = Request::instance();
        $phy_id = $request->get("phy_id");

        $data = Db::query('SELECT `id`,`bianhao` FROM `askteacherhelp` WHERE `flag`="0" and `phy_id`='.$phy_id.' limit 0, 3');
        // 将flag改为1 
        $i = count($data);
        for ( ; $i >=1; $i--) { 
            Db::query('UPDATE `askteacherhelp` SET `flag`="1" WHERE `id`=?',[ $data[$i-1]["id"] ]);
        }
        // dump( $data );
        echo json_encode($data);
    }

    // 获取用户人数
    public function getUserNumber()
    {
        $student = Db::query("select count(id) from physical_student where `role`=?",[0]);
        $teacher = Db::query("select count(id) from physical_student where `role`=?",[1]);
        $administrator = Db::query("select count(id) from physical_student where `role`=?",[2]);
        $data["student"] = $student[0]["count(id)"];
        $data["teacher"] = $teacher[0]["count(id)"];
        $data["administrator"] = $administrator[0]["count(id)"];
        echo json_encode( $data );
    }

    // 获取动态数据
    public function getDynamic()
    {
        $data =array_reverse( Db::query("SELECT * FROM `phy_development` WHERE `status`=1") );
        echo json_encode( $data );
    }
    public function getDynamicAdmin()
    {
        $data =array_reverse( Db::query("SELECT * FROM `phy_development` WHERE 1") );
        echo json_encode( $data );
    }
    // 修改动态状态
    public function changeDynamicStatus()
    {
        //获取数据
        $request = Request::instance();
        $id = $request->get("id");
        $tp = $request->get("tp");
        Db::query("UPDATE `phy_development` SET `status`=? WHERE `id`=?",[$tp,$id]);
        $data =array_reverse( Db::query("SELECT * FROM `phy_development` WHERE 1") );
        $data[0]["res"] = 1;
        echo json_encode( $data );
    }
    // 获取展示数据
    public function getDisplayData()
    {
        //获取数据
        $request = Request::instance();
        $id = $request->get("id");
        $data =array_reverse( Db::query("SELECT * FROM `phy_development` WHERE `id`=?",[$id]) );
        $data = $data[0];
        echo json_encode( $data );
    }
    // 获取对应role的人的数据
    public function getRoleData()
    {
        //获取数据
        $request = Request::instance();
        $role = $request->get("role");
        switch ( $role ) {
            case '0':
                $data = Db::query("SELECT * FROM `physical_student` WHERE `role`=?",[$role]);
                break;

            case '1':
                $data = Db::query("SELECT * FROM `physical_student` WHERE `role`=?",[$role]);
                break;

            case '2':
                $data = Db::query("SELECT * FROM `physical_student` WHERE `role`=?",[$role]);
                break;

            default:
                # code...
                break;
        }
        echo json_encode( $data );
    }
    // 获取图片数据
    public function getImgData()
    {
        $request = Request::instance();
        $type = $request->get("TYPE");

        switch ($type) {
            case 'swiper':
                $data = Db::query("SELECT `value` FROM `phy_configure` WHERE `attribute` IN ('carousel_1','carousel_2','carousel_3')");
                break;

            case 'chomebg':
                $data = Db::query("SELECT `value` FROM `phy_configure` WHERE `attribute` IN ('chomebg')");
                $data = $data[0]["value"];
                break;

            case 'mybg':
                $data = Db::query("SELECT `value` FROM `phy_configure` WHERE `attribute` IN ('mybg')");
                $data = $data[0]["value"];
                break;
            
            default:
                # code...
                break;
        }

        echo json_encode($data);
    }
}