<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class PictureSettingsData
{
	// 当前位置
	var $location = array(
		array("loc" => "图片设置", "href" => "api/admin/pictureSettings"),
	);
	// 轮播图片
	var $carousel;
	// 小程序"我的"背景图
	var $mybg;
	// 小程序"课前必看"背景图
	var $chomebg;

	// 构造函数
	function __construct() 
	{
	    $this->setCarousel();
	    $this->setMybg();
	    $this->setChomebg();
	}

	// 设置轮播图片变量
	function setCarousel()
	{
		$carousel = Db::query("SELECT * FROM `phy_configure` WHERE `attribute` IN ('carousel_1','carousel_2','carousel_3')");
		$this->carousel["carousel"] = $carousel;
	}

	// 设置小程序"我的"背景图变量、
	function setMybg()
	{
		$mybg = Db::query("SELECT * FROM `phy_configure` WHERE `attribute` IN ('mybg')");
		$this->mybg = $mybg[0];
	}

	// 设置小程序"课前必看"背景图变量
	function setChomebg()
	{
		$chomebg = Db::query("SELECT * FROM `phy_configure` WHERE `attribute` IN ('chomebg')");
		$this->chomebg = $chomebg[0];
	}

	// 拼接数据函数
	public function getData()
	{
		// 当前位置数据
		$PictureSettingsData["location"] = $this->location;

		// 轮播图片
		$PictureSettingsData[0] = $this->carousel;

		// "我的"背景图
		$PictureSettingsData[1]["mybg"] = $this->mybg;

		// "课前必看"背景图
		$PictureSettingsData[1]["chomebg"] = $this->chomebg;

		// dump( $PictureSettingsData );
		return $PictureSettingsData ;
	}
}
