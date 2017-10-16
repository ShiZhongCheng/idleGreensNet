<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class UserManagementData
{
	// 当前位置
	var $location = array(
		array("loc" => "用户管理", "href" => "api/admin/userManagement"),
	);

	// 用户数据页数数据和每一页的数据数
	var $pageData,$eachPageData = 20;

	// 构造函数
	function __construct() 
	{
	    $this->setPageData();
	}

	// 设置当前页数据
	function setPageData()
	{
		// 获取当前是第几页和用户类型
		if (empty($_GET["role"])) {
			$role = 0;
		}else{
			$role = $_GET["role"];
		}
		if (empty($_GET["page"])) {
			$page = 1;
		}else{
			$page = $_GET["page"];
		}
		// 获取当前用户类型总数
		$count = Db::query("select count(`id`) from `physical_student` WHERE `role`=?",[$role]);   
		$count = $count[0]["count(`id`)"];
		// 总页数
		$pageNum = ceil( $count / $this->eachPageData );
		// 首页和尾页
		$homePage = 1;
		$endPage  = $pageNum;
		// 上下页
		$lastPage = ($page-1)>0 ? ($page-1) : (1);
		$nextPage = ($page+1)<=$pageNum ? ($page+1) : ($pageNum);
		//计算当前页数起始值 
		if ( $page > $pageNum ) {  
			$page = $pageNum;
		    $num = ($pageNum - 1) * $this->eachPageData;   
		} else {  
		    $num = ($page - 1) * $this->eachPageData;  
		} 
		// 获取数据
		$nowData = Db::query("SELECT * FROM `physical_student` WHERE `role`=? LIMIT ?,?",[$role,$num,$this->eachPageData]); 

		$this->pageData["page"] = $page; // 当前页
		$this->pageData["homePage"] = $homePage; // 首页
		$this->pageData["endPage"] = $endPage;  //尾页
		$this->pageData["lastPage"] = $lastPage;  //上一页
		$this->pageData["nextPage"] = $nextPage;  // 下一页
		$this->pageData["pageNum"] = $pageNum;  // 总页数
		$this->pageData["count"] = $count;  // 总数
		$this->pageData["role"] = $role;  // 角色类型
		$this->pageData["data"] = $nowData;  // 用户数据
	} 

	// 拼接数据函数
	public function getData()
	{
		// 当前位置数据
		$UserManagementData["location"] = $this->location;

		// 用户类型数据
		$UserManagementData[0] = $this->pageData;

		// dump( $UserManagementData );
		return $UserManagementData ;
	}
}