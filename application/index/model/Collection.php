<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Collection extends Model
{
	public function getCollectionData( $distinguish )
	{
		$IndexModel = Loader::model('IndexModel');
        // 获取cookie user_id
        $user_id = $IndexModel->getCookie();
		if ($user_id == -1) {
			return "";
		}
		$distinguish = $distinguish."_";
		$allCollectionData = Db::query("SELECT `collection` FROM `media_user` WHERE `id`=?",[$user_id]);
		// 将字符串分割成为数组
		$allCollectionData = explode(',',$allCollectionData[0]["collection"]); 
		$needCollectionData = array();
		for ($i=0; $i < count($allCollectionData); $i++) { 
			if( strpos($allCollectionData[$i], $distinguish) ){
				array_push( $needCollectionData,substr( $allCollectionData[$i],4 ) );
			}
		}
		return $needCollectionData;
	}
}