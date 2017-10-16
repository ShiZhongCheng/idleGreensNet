<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class SellModel extends Model
{
	public function allClassify()
	{
		$classify = Db::query("SELECT * FROM `media_classify` WHERE 1");
		return $classify;
	}
	public function peoductData()
	{
  		$request = Request::instance();
        if ( isset ($_GET["type"]) ) {
               $type = $request->get("type");   
          } else{ 
               $type = 1;  
          }
          if ( isset ($_GET["page"]) ) {
               $page = $request->get("page");
          } else{
               $page = 1;  
          }
        $Supplyclassification = Loader::model('Supplyclassification');
        $classification = $Supplyclassification->classification(25,$page,$type);   //分页数据
        return $classification;
	}
}