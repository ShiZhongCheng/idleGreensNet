<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Chat extends Model
{
	public function getCollUrMeg()
	{
        	// 获取用户id
                $IndexModel = Loader::model('IndexModel');
                $user_id = $IndexModel->getCookie();

                switch ($user_id) {
                	case -1:
                		$returnData = "";
                		break;
                	
                	default:
                		// 获取用户关注列表
                	    $Collection = Loader::model('Collection');
        		        $userCollection = $Collection->getCollectionData( "ur" );
        		        // 遍历关注列表，获取信息
        		        $returnData = array();
        		        foreach ($userCollection as $id) {
        		        	$userMesg = Db::query("SELECT `id`, `head_image_url`,`nick_name` FROM `media_user` WHERE `id`=?",[$id]);
        		        	array_push($returnData,$userMesg[0]);
        		        }
                		break;
                }
                return $returnData;
	}

        public function getStrangerUrMeg()
        {
                // 获取用户id
                $IndexModel = Loader::model('IndexModel');
                $user_id = $IndexModel->getCookie();

                switch ($user_id) {
                        case -1:
                                $returnData = "";
                                break;
                        
                        default:
                                // 获取用户关注列表
                                $Collection = Loader::model('Collection');
                                $userCollection = $Collection->getCollectionData( "ur" );

                                // 获取所有发送信息给我的人
                                $allUserSeendTome = Db::query("SELECT `send_id` FROM `media_letter` WHERE `receive_id`=? and `type`=?",[$user_id,1]);
                                $allUserSeendTomeId = array();
                                foreach ($allUserSeendTome as $i => $user) {
                                       array_push($allUserSeendTomeId,$user["send_id"]);
                                }

                                // allUserSeendTomeId与userCollection不同的元素
                                $strangerUId = array_diff($allUserSeendTomeId,$userCollection);
                                // 去掉重复值
                                $strangerUId = array_unique($strangerUId);

                                // 遍历关注列表，获取信息
                                $returnData = array();
                                foreach ($strangerUId as $id) {
                                        $userMesg = Db::query("SELECT `id`, `head_image_url`,`nick_name` FROM `media_user` WHERE `id`=?",[$id]);
                                        array_push($returnData,$userMesg[0]);
                                }
                                break;
                }

                return $returnData;
        }
}