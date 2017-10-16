<?php
namespace app\index\model;

use think\Model;
use think\Db;
use think\Loader;

class Search extends Model
{
	public function search($each_disNums,$page,$type,$q)
	{
		$returnData = array();         //返回数组

		// 根据type不同读不同数据
		switch ($type)
		{
			// type=1表示全部
			case 1:
				  // 获取供应大厅数据
				  $data1 = Db::query(" SELECT * FROM `media_provide` WHERE `product_name` LIKE '%".$q."%' OR `product_details` LIKE '%".$q."%' ");
				  // 获取用户
				  $data2 = Db::query(" SELECT * FROM `media_user` WHERE `nick_name` LIKE '%".$q."%' ");
				  // 获取动态
				  $data3 = Db::query(" SELECT `id`, `user_id`, `content`, `imglist`, `time`, `coment_number`, `reading_number` FROM `media_dynamic` WHERE `type`=? and `content` LIKE '%".$q."%' ",[1]);
				  for ($i= 0;$i< count($data3); $i++){ 
		             $com_userMesg = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$data3[$i]["user_id"]]);
		             $data3[$i]["userMesg"] = $com_userMesg[0];
		             $data3[$i]["imglist"] = explode(',',$data3[$i]["imglist"]);
				     array_pop( $data3[$i]["imglist"] ); 
		          } 
		          // 获取代养数据
		          $data4 = Db::query("SELECT * FROM `media_substitutes` WHERE `label1` LIKE '%".$q."%' OR `label2` LIKE '%".$q."%' OR `label3` LIKE '%".$q."%' OR `label4` LIKE '%".$q."%'");
			        $IndexModel = Loader::model('IndexModel');
					for ($i=0; $i < count($data4); $i++) { 
						$data4[$i]["time"] = date("Y-m-d H:i",$data4[$i]["time"]);
						$data4[$i]["userMesg"] = $IndexModel->getIdMesg($data4[$i]["announcer_id"]);
					}
				  // 多个数组拼接成一个数组
				  $data = array_merge($data1,$data2,$data3,$data4);
				  // $data = $data1;
			  break;
			case 2: 
				  // 获取供应大厅数据
				  $data = Db::query(" SELECT * FROM `media_provide` WHERE `product_name` LIKE '%".$q."%' OR `product_details` LIKE '%".$q."%' ");
			  break;
			case 3:
				  // 获取用户
				  $data = Db::query(" SELECT * FROM `media_user` WHERE `nick_name` LIKE '%".$q."%' ");
			  break;
			case 4:
				  // 获取动态
				  $data = Db::query(" SELECT `id`, `user_id`, `content`, `imglist`, `time`, `coment_number`, `reading_number` FROM `media_dynamic` WHERE `type`=? and `content` LIKE '%".$q."%' ",[1]);
				  for ($i= 0;$i< count($data); $i++){ 
		             $com_userMesg = Db::query("SELECT `id`, `nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$data[$i]["user_id"]]);
		             $data[$i]["userMesg"] = $com_userMesg[0];
		             $data[$i]["imglist"] = explode(',',$data[$i]["imglist"]);
				     array_pop( $data[$i]["imglist"] ); 
		          } 
			  break;
			case 5:
					// 获取代养数据
					$data = Db::query("SELECT * FROM `media_substitutes` WHERE `label1` LIKE '%".$q."%' OR `label2` LIKE '%".$q."%' OR `label3` LIKE '%".$q."%' OR `label4` LIKE '%".$q."%'");
			        $IndexModel = Loader::model('IndexModel');
					for ($i=0; $i < count($data); $i++) { 
						$data[$i]["time"] = date("Y-m-d H:i",$data[$i]["time"]);
						$data[$i]["userMesg"] = $IndexModel->getIdMesg($data[$i]["announcer_id"]);
					}
				break;
			default:
			  break;
		}
		// dump($data);
		  // 将数组顺序打乱
		 if (!empty($data)) {
			 shuffle( $data );
		  }
		  // 计算数据长度，用于分页
		  $count = count( $data );
		  // 总页数
		  $pageNum = ceil( $count / $each_disNums );
		  //计算当前页数起始值 
		  if ( $page > $pageNum ) {  
		      $num = 0;  
		  } else {  
		      $num = ($page - 1) * $each_disNums;  
		  }

		  // 获取当前页的数据
		  $current_page_data = array();
		  for ($i=0; $i < $each_disNums && $num < $count; $i++,$num++) { 
			  array_push($current_page_data, $data[$num] );
		  }
		  // 上下页
		  $lastPage = ($page-1)>0 ? ($page-1) : (1);
		  $nextPage = ($page+1)<=$pageNum ? ($page+1) : ($pageNum);

		  // 首页和尾页
		  $home_page = "__ROOT__/search?q=".$q."&type=".$type."&page=1";
		  $end_page  = "__ROOT__/search?q=".$q."&type=".$type."&page=".$pageNum;

		  // 拼接返回数组
		  array_push($returnData,"__ROOT__/search?q=".$q."&type=".$type."&page=".$lastPage, "__ROOT__/search?q=".$q."&type=".$type."&page=".$nextPage,$home_page,$end_page,$count,$pageNum,$page,$type,$current_page_data,$q);
		  return $returnData;
	}

}