<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class CourseSettingsData
{
	// 当前位置
	var $location = array(
		array("loc" => "课前必看", "href" => "api/admin/courseSettings"),
	);

	// 课程
	var $course;

	// 构造函数
	function __construct() 
	{
	    $this->setCourse();
	}

	// 设置课程变量
	function setCourse()
	{
		$course = Db::query("SELECT * FROM `physical_name` WHERE 1");
		$this->course["course"] = $course;
	}

	// 拼接数据函数
	public function getData()
	{
		// 当前位置数据
		$CourseSettingsData["location"] = $this->location;

		// 课程数据
		$CourseSettingsData[0] = $this->course;

		// dump( $CourseSettingsData );
		return $CourseSettingsData ;
	}
}