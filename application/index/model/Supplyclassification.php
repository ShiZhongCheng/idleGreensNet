<?php
namespace app\index\model;

use think\Model;
use think\Db;

class Supplyclassification extends Model
{
	public function classification($each_disNums,$page,$type)
	{
		$returnData = array();         //返回数组

		//获取对应类别总数量
		if ($type == 1) {
			$count = Db::query("select count(`sort_id`) from `media_provide` WHERE 1");   
			$count = $count[0]["count(`sort_id`)"];
		}else{
			$count = Db::query("select count(`sort_id`) from `media_provide` WHERE `product_type`=?",[$type]);   
			$count = $count[0]["count(`sort_id`)"];
		}

		// 总页数
		$pagenum = ceil( $count / $each_disNums );

		// 首页和尾页
		$home_page = "__ROOT__/sell?type=".$type."&page=1";
		$end_page  = "__ROOT__/sell?type=".$type."&page=".$pagenum;

		//计算当前页数起始值 
		if ( $page > $pagenum ) {  
		    $num = 0;  
		} else {  
		    $num = ($page - 1) * $each_disNums;  
		} 

		if ($type == 1) {
			$nowData = Db::query("SELECT * FROM `media_provide` WHERE `product_state`=? LIMIT ?,?",[1,$num,$each_disNums]); 
		}else{
			$nowData = Db::query("SELECT * FROM `media_provide` WHERE `product_state`=? and `product_type`=? LIMIT ?,?",[1,$type,$num,$each_disNums]); 
		}
		// 打乱数组
        shuffle( $nowData );

		// 上下页
		$lastPage = ($page-1)>0 ? ($page-1) : (1);
		$nextPage = ($page+1)<=$pagenum ? ($page+1) : ($pagenum);

		array_push($returnData, "__ROOT__/sell?type=".$type."&page=".$lastPage, "__ROOT__/sell?type=".$type."&page=".$nextPage, $pagenum ,$nowData,$count,$pagenum,$home_page,$end_page,$page,$type);

		// dump( $returnData );

		return $returnData;
	}
}