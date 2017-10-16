<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class IndexData
{
	// 当前位置
	var $location = array(
		array("loc" => "首页", "href" => "api/admin/index"),
	);
	// 实验数据
	var $experiment;
	// 报修数据
	var $repairs;
	// 人数统计
	var $user;
	// 小程序显示通告统计
	var $notice;
	// 小程序轮播图
	var $carousel;

	// 构造函数
	function __construct() 
	{
	    $this->setExperiment();
	    $this->setRepairs();
	    $this->setUser();
	    $this->setNotice();
	    $this->setCarousel();
	}
	// 设置实验变量
	function setExperiment()
	{
		// 已经完成实验总数
		$finshEx = Db::query("SELECT COUNT(`id`) FROM `teacherusehistory` WHERE `isend`=1");
		$finshEx = $finshEx[0]["COUNT(`id`)"];
		$this->experiment["finshEx"] = $finshEx;

		// 目前正在进行的实验
		$nowEx = Db::query("SELECT COUNT(`id`) FROM `teacherusehistory` WHERE `isend`=0");
		$nowEx = $nowEx[0]["COUNT(`id`)"];
		$this->experiment["nowEx"] = $nowEx;

		// 现开设实验              
		$foundEx = Db::query("SELECT COUNT(`phy_id`) FROM `physical_name` WHERE 1"); 
		$foundEx = $foundEx[0]["COUNT(`phy_id`)"];
		$this->experiment["foundEx"] = $foundEx; 

        // 已经上传视频实验
        $haveVideoEx = Db::query("SELECT COUNT(`phy_id`) FROM `physical_name` WHERE `phy_movie_leng`!=0"); 
        $haveVideoEx = $haveVideoEx[0]["COUNT(`phy_id`)"];
		$this->experiment["haveVideoEx"] = $haveVideoEx; 
	}
	// 设置报修变量
	function setRepairs()
	{
		// 报修总数
		$allRep = Db::query("SELECT COUNT(`id`) FROM `physical_baoxiu` WHERE 1");
		$allRep = $allRep[0]["COUNT(`id`)"];
		$this->repairs["allRep"] = $allRep;

		// 未处理报修
		$unDealRep = Db::query("SELECT COUNT(`id`) FROM `physical_baoxiu` WHERE `flag`=0");
		$unDealRep = $unDealRep[0]["COUNT(`id`)"];
		$this->repairs["unDealRep"] = $unDealRep;
	}
	// 设置用户数据
	function setUser()
	{
		// 学生
		$student = Db::query("SELECT COUNT(`id`) FROM `physical_student` WHERE `role`=0");
		$student = $student[0]["COUNT(`id`)"];
		$this->user["student"] = $student;

		// 管理员
		$administrators = Db::query("SELECT COUNT(`id`) FROM `physical_student` WHERE `role`=1");
		$administrators = $administrators[0]["COUNT(`id`)"];
		$this->user["administrators"] = $administrators;

		// 教师
		$teacher = Db::query("SELECT COUNT(`id`) FROM `physical_student` WHERE `role`=2");
		$teacher = $teacher[0]["COUNT(`id`)"];
		$this->user["teacher"] = $teacher;
	}
	// 设置小程序显示通告数据
	function setNotice()
	{
		$notice = Db::query("SELECT `id`, `title`, `time`, `announcer`, `img` FROM `phy_development` WHERE `status`=1");
		$this->notice["notice"] = array_reverse($notice);
	}
	// 设置轮播图数据
	function setCarousel()
	{
		$carousel = Db::query("SELECT * FROM `phy_configure` WHERE `attribute` IN ('carousel_1','carousel_2','carousel_3')");
		$this->carousel["carousel"] = $carousel;
	}
	// 拼接数据函数
	public function getData()
	{
		// 当前位置数据
		$IndexData["location"] = $this->location;

		// 实验和报修数据
		$IndexData[0]["Ex"] = $this->experiment;
		$IndexData[0]["Rep"] = $this->repairs;

		// 用户数据
		$IndexData[1] = $this->user;

		// 显示通告数据
		$IndexData[2] = $this->notice;

		// 小程序轮播图数据
		$IndexData[3] = $this->carousel;

		// dump( $IndexData );
		return $IndexData ;
	}
}