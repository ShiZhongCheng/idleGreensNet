<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Comment extends Model
{
	public function searchData()
	{
		$request = Request::instance();
        $q = $request->get("q");
        // 得到当前type
        if (isset ($_GET["type"])) { 
            $type = $_GET["type"];  
        } else {  
            $type = 1;  
        }
        //得到当前是第几页  
        if (isset ($_GET["page"])) { 
            $page = $_GET["page"];  
        } else {  
            $page = 1;  
        }  

        $Search = Loader::model('Search');
        $search_page_mesg = $Search->search(5,$page,$type,$q);   //分页数据
        return $search_page_mesg;
	}
    public function sellerData($id)
    {
        $returnData = array();

        $qt_user_id = $id;
        $qt_user_mesg = Db::query("SELECT `id`, `head_image_url`,`nick_name`,`my_home_page_bg`,`supplier` FROM `media_user` WHERE `id`=?",[$qt_user_id]);
        
        $returnData["head_image_url"] = $qt_user_mesg[0]["head_image_url"];       //商家头像
        $returnData["nick_name"] = $qt_user_mesg[0]["nick_name"];                 //商家昵称
        $returnData["id"] = $qt_user_mesg[0]["id"];                               //商家id
        $returnData["supplier"] = $qt_user_mesg[0]["supplier"];                   //供应商标志位
        $returnData["my_home_page_bg"] = $qt_user_mesg[0]["my_home_page_bg"];     //供应商主页大图

        if ($qt_user_mesg[0]["supplier"] == 0) {
            $returnData["business_introduce"] = "";
            $returnData["loction"] = ""; 
            return $returnData;
        }

        $business_mesg = Db::query("SELECT `business_introduce`,`loction` FROM `media_supplier` WHERE `id`=?",[$qt_user_id]);

        $returnData["business_introduce"] = $business_mesg[0]["business_introduce"]; //商家介绍
        $returnData["loction"] = $business_mesg[0]["loction"]; 

        return $returnData;
    }

    // 获取出众农产品
    public function getGooDSell()
    {
        $typeDa = Db::query("SELECT * FROM `media_provide` WHERE `product_id` in('c02kf5','zw0toe','f3fgki','ypz4ib','g27jfs')");
        $IndexModel = Loader::model('IndexModel');
        for ($i=0; $i < count($typeDa); $i++) { 
            $typeDa[$i]["userMesg"] = $IndexModel->getIdMesg($typeDa[$i]["id"]);
        }
        return $typeDa;
    }

    // 获取热门动态
    public function getHotDy()
    {
        $returnData = Db::query("SELECT `id`, `user_id`, `content`, `imglist`,`reading_number` FROM `media_dynamic` WHERE `id` in(35,36,34)");
        $IndexModel = Loader::model('IndexModel');
        for ($i=0; $i < count($returnData); $i++) { 
            $returnData[$i]["userMesg"] = $IndexModel->getIdMesg($returnData[$i]["user_id"]);
            $returnData[$i]["imglist"] = explode(",", $returnData[$i]["imglist"]);
        }
        return $returnData;
    }

    // 获取首页推荐代养
    public function indexFoster()
    {
        $returnData = Db::query("SELECT `id`, `substitutes_id`, `title`, `cover_img` FROM `media_substitutes` WHERE id in(7,6,22,23,24)");
        return $returnData;
    }
}