<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Dynamicdata extends Model
{
	public function getDyData($user_id,$type,$each_disNums)
	{
		$request = Request::instance();
        if ( isset ($_GET["p"]) ) 
        {
               $page = $request->get("p");         
        } else
        {
               $page = 1;         
        }

        $returnData = array();         //返回数组

		//获取对应类别总数量
		$count = Db::query("select count(`id`) from `media_dynamic` WHERE `user_id`=? and `type`=?",[$user_id,$type]);   
		$count = $count[0]["count(`id`)"];

		// 总页数
		$pagenum = ceil( $count / $each_disNums );

		// 首页和尾页
		$home_page = 1;
		$end_page  = $pagenum;

		//计算当前页数起始值 
		if ( $page > $pagenum ) {  
		    $num = 0;  
		} else {  
		    $num = ($page - 1) * $each_disNums;  
		} 

		$nowData = Db::query("SELECT * FROM `media_dynamic` WHERE `user_id`=? and `type`=? LIMIT ?,?",[$user_id,$type,$num,$each_disNums]); 

		for ($i= 0;$i< count($nowData); $i++){ 
            $nowData[$i]["imglist"] = explode(',',$nowData[$i]["imglist"]);
            array_pop( $nowData[$i]["imglist"] ); 
        } 

		// 上下页
		$lastPage = ($page-1)>0 ? ($page-1) : (1);
		$nextPage = ($page+1)<=$pagenum ? ($page+1) : ($pagenum);

		array_push($returnData, $lastPage, $nextPage, $pagenum ,$nowData,$count,$pagenum,$home_page,$end_page,$page,$type);

		// dump( $returnData );

		return $returnData;
	}
}