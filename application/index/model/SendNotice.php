<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class SendNotice extends Model
{
	public function sendNotice($sender,$receiver,$title,$content,$type,$Corresponding_id)
	{
		// 日期转换为UNIX时间戳
        $time = strtotime( date('Y-m-d H:i') );
        Db::query("INSERT INTO `media_notice`(`sender`, `receiver`, `title`, `content`, `Corresponding_id`,`type`, `time`, `static`) VALUES (?,?,?,?,?,?,?,?)",[$sender,$receiver,$title,$content,$Corresponding_id,$type,$time,0]);
        return 1;
	}
	// 获取用户通知未读数目
	public function getNotReadNumber($user_id)
	{
		// 获取对应substitutes_id的成长记录总数
		$count = Db::query("select count(`id`) from `media_notice` WHERE `receiver`=? and `static`=?",[$user_id,0]);   
		$count = $count[0]["count(`id`)"];
		return $count;
	}
	// 获取用户未读消息或者已读消息
	public function getNotice($type,$user_id)
	{
		switch ($type) {
			// 未读消息
			case 0:
				$noticeData = Db::query("SELECT `id`, `sender`, `receiver`, `title`, `Corresponding_id`, `type`, `time` FROM `media_notice` WHERE `receiver`=? and `static`=?",[$user_id,0]);
				for ($i=0; $i < count($noticeData); $i++) { 
					$noticeData[$i]["time"] = date('Y-m-d H:i',$noticeData[$i]["time"]);
				}
				break;
			
			// 已读消息
			case 1:
				$noticeData = Db::query("SELECT `id`, `sender`, `receiver`, `title`, `Corresponding_id`, `type`, `time` FROM `media_notice` WHERE `receiver`=? and `static`=?",[$user_id,1]);
				for ($i=0; $i < count($noticeData); $i++) { 
					$noticeData[$i]["time"] = date('Y-m-d H:i',$noticeData[$i]["time"]);
				}
				break;
		}

		return $noticeData;
	}
	// 获取某条具体通知
	public function getOneDetailed($id)
	{
		$returnData = Db::query("SELECT * FROM `media_notice` WHERE `id`=?",[$id]);
		$returnData = $returnData[0];
		$returnData["time"] = date('Y-m-d H:i',$returnData["time"]);
		// 通知为代养订单
		if ($returnData["type"] == 2) {
			// 获取发送者信息
	        $IndexModel = Loader::model('IndexModel');
	        $senderMesg = $IndexModel->getIdMesg( $returnData["sender"] );
	        $returnData["senderMesg"] = $senderMesg;

	        // 获取对应商品信息
	        $order = Db::query('SELECT `Corresponding_id` FROM `media_order` WHERE `id`=?',[ $returnData["Corresponding_id"] ]);
	        $order = $order[0]["Corresponding_id"];
	        $substitutesMesg = Db::query("SELECT * FROM `media_substitutes` WHERE `substitutes_id`=?",[$order]);
	        $returnData["substitutesMesg"] = $substitutesMesg[0];
		}
		// 通知为认养成长记录
		if ($returnData["type"] == 4) {
			// 获取发送者信息
	        $IndexModel = Loader::model('IndexModel');
	        $senderMesg = $IndexModel->getIdMesg( $returnData["sender"] );
	        $returnData["senderMesg"] = $senderMesg;
		}

		// 标记消息为已读状态
		Db::query("UPDATE `media_notice` SET `static`=? WHERE `id`=?",[1,$id]);
		return $returnData;
	}
}