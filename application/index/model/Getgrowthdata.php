<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Getgrowthdata extends Model
{
	public function growthData($each,$substitutes_id,$page)
	{
		// 获取对应substitutes_id的成长记录总数
		$count = Db::query("select count(`id`) from `media_growth` WHERE `substitutes_id`=?",[$substitutes_id]);   
		$count = $count[0]["count(`id`)"];

		// 总页数
		$pagenum = ceil( $count / $each );

		// 计算下一页
		if ( $page > $pagenum ) {  
		    $nextpage = 0;
		} else {  
		    $nextpage = $page + 1;
		} 

		// 倒置数据，把page倒置
		$page = $pagenum - ($page - 1);

		//计算当前页数起始值
		if ( $page > $pagenum ) {  
		    $num = 0;  
		} else {  
		    $num = ($page - 1) * $each;  
		} 

		// 获取当前页数据
		$pageData = Db::query("SELECT * FROM `media_growth` WHERE `substitutes_id`=? LIMIT ?,?",[$substitutes_id,$num,$each]);
		for ($i=0; $i < count($pageData); $i++) { 
			$pageData[$i]["time"] = date("y-m-d",$pageData[$i]["time"]);
		}
		// 降序排序
		array_multisort($pageData,SORT_DESC);

		// 返回数据拼接
		$returnData = array();
		$returnData["nextpage"] = $nextpage;
		$returnData["data"] = $pageData;
		return $returnData;
	}
	public function getGrowthComment($each,$substitutes_id,$page)
	{
		// 获取对应substitutes_id的成长记录总数
		$count = Db::query("select count(`id`) from `media_dynamic` WHERE `correspond_id`=? and `type`=?",[$substitutes_id,3]);   
		$count = $count[0]["count(`id`)"];

		// 总页数
		$pagenum = ceil( $count / $each );

		// 计算下一页
		if ( $page > $pagenum ) {  
		    $nextpage = 0;
		} else {  
		    $nextpage = $page + 1;
		} 

		// 倒置数据，把page倒置
		$page = $pagenum - ($page - 1);

		//计算当前页数起始值
		if ( $page > $pagenum ) {  
		    $num = 0;  
		} else {  
		    $num = ($page - 1) * $each;  
		} 

		// 获取当前页数据
		$pageData = Db::query("SELECT `user_id`, `correspond_user_id`, `content`, `time` FROM `media_dynamic` WHERE `correspond_id`=? LIMIT ?,?",[$substitutes_id,$num,$each]);
		$IndexModel = Loader::model('IndexModel');
		for ($i=0; $i < count($pageData); $i++) { 
			$pageData[$i]["time"] = date("y-m-d H:i",$pageData[$i]["time"]);
			$pageData[$i]["userMesg"] = $IndexModel->getIdMesg( $pageData[$i]["user_id"] );
		}
		// 降序排序
		array_multisort($pageData,SORT_DESC);

		// 返回数据拼接
		$returnData = array();
		$returnData["nextpage"] = $nextpage;
		$returnData["data"] = $pageData;
		return $returnData;
	}

	// 获取认养人
	public function getBuyer( $substitutes_id )
	 {
	 	$buyerIdarry = Db::query("SELECT `buyer` FROM `media_order` WHERE `Corresponding_id`=? and `static`=?",[$substitutes_id,1]);

	 	$returnData = array();
	 	$IndexModel = Loader::model('IndexModel');
	 	for ($i=0; $i < count($buyerIdarry); $i++) { 
	 		$returnData[$i] = $IndexModel->getIdMesg( $buyerIdarry[$i]["buyer"] );
	 	} 

	 	return $returnData;
	 } 
}