<?php
namespace app\api\model;
use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class ExperimentalRecord
{
	// 当前位置
	var $location = array(
		array("loc" => "实验记录", "href" => "api/admin/experimentalRecord"),
	);

	// 实验记录数据变量和每页显示多少数据变量
	var $history,$eachPageData = 20;

	// 构造函数
	function __construct() 
	{
	    $this->setHistory();
	}

	// 设置实验记录值
	function setHistory()
	{
		// 获取显示规则和第几页
		if (empty($_GET["openid"])) {
			$openid = "all";
		}else{
			$openid = $_GET["openid"];
		}
		if (empty($_GET["page"])) {
			$page = 1;
		}else{
			$page = $_GET["page"];
		}

		switch ($openid) {
			case 'all':
				// 获取规则类型总数
				$count = Db::query("select count(`id`) from `teacherusehistory` WHERE 1");   
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
				$nowData = Db::query("SELECT * FROM `teacherusehistory` WHERE 1 LIMIT ?,?",[$num,$this->eachPageData]);
				$nowData = array_reverse($nowData);
				break;
			
			default:
				// 获取规则类型总数
				$count = Db::query("select count(`id`) from `teacherusehistory` WHERE `oppenid`=?",[$openid]);   
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
				$nowData = Db::query("SELECT * FROM `teacherusehistory` WHERE `oppenid`=? LIMIT ?,?",[$openid,$num,$this->eachPageData]);
				$nowData = array_reverse($nowData);
				break;
		}
		$this->history["page"] = $page; // 当前页
		$this->history["homePage"] = $homePage; // 首页
		$this->history["endPage"] = $endPage;  //尾页
		$this->history["lastPage"] = $lastPage;  //上一页
		$this->history["nextPage"] = $nextPage;  // 下一页
		$this->history["pageNum"] = $pageNum;  // 总页数
		$this->history["count"] = $count;  // 总数
		$this->history["openid"] = $openid;  // 角色类型
		$this->history["data"] = $nowData;  // 用户数据

	}

	// 拼接数据函数
	public function getData()
	{
		// 当前位置数据
		$ExperimentalRecordData["location"] = $this->location;

		// 实验记录数据
		$ExperimentalRecordData[0] = $this->history;

		// dump( $ExperimentalRecordData );
		return $ExperimentalRecordData ;
	}
}
