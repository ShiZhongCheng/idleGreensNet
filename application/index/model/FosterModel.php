<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class FosterModel extends Model
{
	public function getFosterData()
	{
		// 获取页数
		if ( empty($_GET["page"]) ) {
			$page = 1;
		}else{
			$page = $_GET["page"];
		}
		$FosterModel = $IndexModel = Loader::model('FosterModel');
		return $FosterModel->dataPaging(15,$page);
	}
	public function dataPaging($each_disNums,$page)
	{
		//返回数组
		$returnArry = array();     
		// 获取代养数据总数
		$count = Db::query("select count(`id`) from `media_substitutes` WHERE 1");   
		$dataCount = $count[0]["count(`id`)"];
		//数据为空时返回空数组
		if ($dataCount == 0) {
			// 返回数组前两数据，用于上一页和下一页
			array_push($returnArry,0,0,0,0,1);      
			return $returnArry;       
		}

		//数据总共可以分页，总页数
		$pageNums = ceil( $dataCount/$each_disNums );  

		//如果当前页大于总页数时，返回最后一页数据
		if ($page > $pageNums) {  
			$page = $pageNums;  
		}
		//当前页数据开始下标   
		$startIndent = ($page-1)*$each_disNums;    

		// 正常数据
		$returnArry["lastPage"] = ($page-1)>0 ? ($page-1) : (1);                             //上一页
		$returnArry["nextPage"] = ($page+1)<=$pageNums ? ($page+1) : ($pageNums);            //下一页
		$returnArry["allData"]  = $dataCount;                                                //总数据
		$returnArry["allPageNumber"] = $pageNums;                                            //总共可分页数
		$returnArry["nowPage"] = $page;                                                      //当前页  

		$da = Db::query("SELECT `id`, `substitutes_id`, `title`, `label1`, `label2`, `label3`, `label4`, `cover_img`, `quotes`, `time`, `announcer_id` FROM `media_substitutes` WHERE 1 LIMIT ?,?",[$startIndent,$each_disNums]);  
		$IndexModel = Loader::model('IndexModel');
		for ($i=0; $i < count($da); $i++) { 
		   	$da[$i]["time"] = date('Y-m-d H:i',$da[$i]["time"]);
		   	$da[$i]["userMesg"] = $IndexModel->getIdMesg( $da[$i]["announcer_id"] );
		} 
		$returnArry["data"] = $da;
		return $returnArry;	  
	}

	// 获取某个人发布的代养
	public function getUserFoster($announcer_id)
	{
		// 获取页数
		if ( empty($_GET["pg"]) ) {
			$page = 1;
		}else{
			$page = $_GET["pg"];
		}
		$FosterModel = $IndexModel = Loader::model('FosterModel');
		return $FosterModel->getUserFosterFun(15,$page,$announcer_id);
	}
	public function getUserFosterFun($each_disNums,$page,$announcer_id)
	{
		//返回数组
		$returnArry = array();     
		// 获取代养数据总数
		$count = Db::query("select count(`id`) from `media_substitutes` WHERE 1");   
		$dataCount = $count[0]["count(`id`)"];
		//数据为空时返回空数组
		if ($dataCount == 0) {
			// 返回数组前两数据，用于上一页和下一页
			array_push($returnArry,0,0,0,0,1);      
			return $returnArry;       
		}

		//数据总共可以分页，总页数
		$pageNums = ceil( $dataCount/$each_disNums );  

		//如果当前页大于总页数时，返回最后一页数据
		if ($page > $pageNums) {  
			$page = $pageNums;  
		}
		//当前页数据开始下标   
		$startIndent = ($page-1)*$each_disNums;    

		// 正常数据
		$returnArry["lastPage"] = ($page-1)>0 ? ($page-1) : (1);                             //上一页
		$returnArry["nextPage"] = ($page+1)<=$pageNums ? ($page+1) : ($pageNums);            //下一页
		$returnArry["allData"]  = $dataCount;                                                //总数据
		$returnArry["allPageNumber"] = $pageNums;                                            //总共可分页数
		$returnArry["nowPage"] = $page;                                                      //当前页  

		$da = Db::query("SELECT `id`, `substitutes_id`, `title`, `label1`, `label2`, `label3`, `label4`, `cover_img`, `quotes`, `time`, `announcer_id` FROM `media_substitutes` WHERE `announcer_id`=? LIMIT ?,?",[$announcer_id,$startIndent,$each_disNums]);  
		$IndexModel = Loader::model('IndexModel');
		for ($i=0; $i < count($da); $i++) { 
		   	$da[$i]["time"] = date('Y-m-d H:i',$da[$i]["time"]);
		   	$da[$i]["userMesg"] = $IndexModel->getIdMesg( $da[$i]["announcer_id"] );
		} 
		$returnArry["data"] = $da;
		return $returnArry;	 
	}
}