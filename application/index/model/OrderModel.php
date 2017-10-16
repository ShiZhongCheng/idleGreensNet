<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class OrderModel extends Model
{
	public function getMyOrder( $buyer,$type,$static )
	{
		$orderData = Db::query("SELECT * FROM `media_order` WHERE `buyer`=? and `type`=? and `static`=?",[$buyer,$type,$static]);
		$IndexModel = Loader::model('IndexModel');
		for ($i=0; $i < count($orderData); $i++) { 
			$orderData[$i]["time"] = date('Y-m-d H:i',$orderData[$i]["time"]);

			$orderData[$i]["sellerMesg"] = $IndexModel->getIdMesg( $orderData[$i]["seller"] );

			// 如果是代养订单获取代养信息
			if ($type == 2) {
				$goodsMesg = Db::query("SELECT `substitutes_id`, `title`, `label1`, `label2`, `label3`, `label4`, `content`, `cover_img`, `quotes`, `time` FROM `media_substitutes` WHERE `substitutes_id`=?",[$orderData[$i]["Corresponding_id"]]);
				$orderData[$i]["goodsMesg"] = $goodsMesg[0];
			}
		}
		return $orderData;
	}
}