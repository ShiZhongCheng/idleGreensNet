<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class RealTimeData
{
	// 当前位置
	var $location = array(
		array("loc" => "实验记录", "href" => "api/admin/experimentalRecord"),
		array("loc" => "实时情况", "href" => ""),
	);
	// 实验信息
	var $exMesg;
	// 当前实验座位
	var $seat;
	// 用户数据
	var $userData;

	// 构造函数
	function __construct() 
	{
		$this->setExMesg();
	    $this->setSeat();
	    $this->setUserData();
	}

	function setExMesg()
	{
		if (empty($_GET["ex_id"]) || empty($_GET["phy_id"])) {
			// 跳转到实验记录
            header("Location: ".$_SERVER['SCRIPT_NAME']."/api/admin/experimentalRecord");
            exit();
		}
		$this->exMesg["ex_id"] = $_GET["ex_id"];
		$this->exMesg["phy_id"] = $_GET["phy_id"];
	}

	function setSeat()
	{
		$seat = Db::query("SELECT `bianhao` FROM `phy_".$this->exMesg["phy_id"]."` WHERE 1");
		$this->seat = $seat;
	}

	// 设置用户数据
	function setUserData()
	{
		$userData = Db::query("SELECT `id`, `name`, `oppenid`, `bianhao`, `ex_id`, `end_time` FROM `phy_".$this->exMesg["phy_id"]."_usehistory` WHERE `ex_id`=?",[$this->exMesg["ex_id"]]);
		Db::query("UPDATE `phy_".$this->exMesg["phy_id"]."_usehistory` SET `flag`=? WHERE `ex_id`=?",[1,$this->exMesg["ex_id"]]);
		$this->userData = $userData;
	}

	// 拼接数据函数
	public function getData()
	{
		// 当前位置数据
		$RealTimeData["location"] = $this->location;

		// 实验信息
		$RealTimeData["exMesg"] = $this->exMesg;

		// 座位信息
		$RealTimeData["seat"] = $this->seat;

		// 用户数据
		$RealTimeData["userData"] = $this->userData;

		// dump( $RealTimeData );
		return $RealTimeData ;
	}
}
