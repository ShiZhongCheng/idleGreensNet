<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class IndexModel extends Model
{
	public function getUseMesg()
	{
    		$IndexModel = Loader::model('IndexModel');
        // 获取cookie user_id
        $user_id = $IndexModel->getCookie();
        if ( $user_id != -1 ) {
        	$userMesg = Db::query("SELECT `id`, `head_image_url`,`nick_name`,`my_home_page_bg`,`supplier` FROM `media_user` WHERE `id`=?",[$user_id]);
        	$userMesg = $userMesg[0];
        }else{
        	$userMesg = "";
        }
        return $userMesg;
	}
  public function getIdMesg($user_id)
  {
    if ( $user_id != -1 ) {
          $userMesg = Db::query("SELECT `id`, `head_image_url`,`nick_name`,`my_home_page_bg`,`supplier`,`address`,`phone_number` FROM `media_user` WHERE `id`=?",[$user_id]);
          $userMesg = $userMesg[0];
        }else{
          $userMesg = "";
        }
        return $userMesg;
  }
	public function setCookie()
	{
		$request = Request::instance();
        if ( !empty($request->get()) && $request->get("remberPassword")=="true" ) {
            $nick_name = $request->get("nick_name");
            $password = $request->get("password");
            $id = $request->get("id");
            // cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>86400,'path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('id');
            Cookie::set('id',$id);
        }else if ( !empty($request->get()) && $request->get("remberPassword")=="false" ) {
            $nick_name = $request->get("nick_name");
            $password = $request->get("password");
            $id = $request->get("id");
            // cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>'','path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('id');
            Cookie::set('id',$id);
        }

        $loction = $request->get("loction");
        if ( isset( $loction ) ) {
        	// cookie初始化
            Cookie::init(['prefix'=>'user_','expire'=>'','path'=>'/']);
            // 删除指定前缀的cookie
            Cookie::delete('loction');
            Cookie::set('loction',$loction );
        }
        return 0;
	}
  public function delCookie()
  {
    // cookie初始化
    Cookie::init(['prefix'=>'user_','expire'=>'','path'=>'/']);
    // 删除指定前缀的cookie
    Cookie::delete('id');
    return 1;
  }
	public function getUserLoction()
	{
		// 判断是否有cookie
        if ( !Cookie::has('loction','user_') ) {
            return -1;      //表示没有cookie
        }
        $user_loction = Cookie::get('loction','user_');    //获取cookie
        return $user_loction;
	}
	public function getCookie()
	{
		// 判断是否有cookie
        if ( !Cookie::has('id','user_') ) {
            return -1;      //表示没有cookie
        }
        $user_id = Cookie::get('id','user_');    //获取cookie
        return $user_id;
	}
	public function loginLonctionNoId( $login_loction,$show_number )
	{
		// 获取所有供应商的位置
        $all_supplier = Db::query("SELECT `id`, `loction` FROM `media_supplier` WHERE 1"); 

        //相距在10000米以内的供应商id数组
        $loction_supplier_id = array();     

        //实例化GetDistance模型,用于计算两点之间的距离,遍历所有供应商
        $GetDistance = Loader::model('GetDistance');
        foreach ($all_supplier as $supplier) {
          if ( $GetDistance->getDistance($login_loction,$supplier["loction"]) < 10000 )  {
              $supplierA1 = array();
              $supplierA1["id"] = $supplier["id"];
              $supplierA1["distance"] = $GetDistance->getDistance($login_loction,$supplier["loction"]);
              $supplierA1["loction"] = $supplier["loction"];
              array_push($loction_supplier_id,$supplierA1);
          }
        }
        // 遍历附近供应商id数组
        $loction_supplier_mesg = array();           //相距在10000米以内的供应商信息
        foreach ($loction_supplier_id as $loction_supplier_user_id) {
           $loc_suuer_data = Db::query("SELECT `id`,`nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$loction_supplier_user_id["id"]]);
           $supplierA2 = array();
           $supplierA2["id"] = $loction_supplier_user_id["id"];
           $supplierA2["distance"] = $loction_supplier_user_id["distance"];
           $supplierA2["loction"] = $loction_supplier_user_id["loction"];
           $supplierA2["nick_name"] = $loc_suuer_data[0]["nick_name"];
           $supplierA2["head_image_url"] = $loc_suuer_data[0]["head_image_url"];
           array_push($loction_supplier_mesg,$supplierA2);
        }
        // 打乱数组
        shuffle( $loction_supplier_mesg );
        for ($i= 0;$i< count($loction_supplier_mesg); $i++){ 
          // 提供商品
            $provide_count = Db::query("select count(`sort_id`) from `media_provide` WHERE `id`=? and `product_state`=?",[$loction_supplier_mesg[$i]["id"],1]);   
            $loction_supplier_mesg[$i]["provide_count"] = $provide_count[0]["count(`sort_id`)"];

            // 动态
            $dynamic_count = Db::query("select count(`id`) from `media_dynamic` WHERE `user_id`=? and `type`=? and `status`=?",[$loction_supplier_mesg[$i]["id"],1,1]);   
            $loction_supplier_mesg[$i]["dynamic_count"] = $dynamic_count[0]["count(`id`)"];
        } 

        //实例化Pagination模型,用于分页
        //得到当前是第几页  
        if (isset ($_GET["page"])) { //是否存在"id"的参数  
            $page = $_GET["page"];  
        } else {  
            $page = 1;  
        }  
        $Pagination = Loader::model('Pagination');
        $page_mesg = $Pagination->pagination($loction_supplier_mesg,$show_number,$page);   //分页数据

        return $page_mesg;
	}
	public function loginLonction( $user_id )
	{
		// 判断用户是否有上次登录位置
        $login_loction = Db::query("SELECT `login_loction` FROM `media_user` WHERE `id`=?",[$user_id]);
        $login_loction = $login_loction[0]["login_loction"];
        if ($login_loction == "") {
           return -1;        //没有直接返回-1
        }

        // 获取所有供应商的位置
        $all_supplier = Db::query("SELECT `id`, `loction` FROM `media_supplier` WHERE 1"); 

        //相距在10000米以内的供应商id数组
        $loction_supplier_id = array();     

        //实例化GetDistance模型,用于计算两点之间的距离,遍历所有供应商
        $GetDistance = Loader::model('GetDistance');
        foreach ($all_supplier as $supplier) {
          if ( $GetDistance->getDistance($login_loction,$supplier["loction"]) < 10000 )  {
              $supplierA1 = array();
              $supplierA1["id"] = $supplier["id"];
              $supplierA1["distance"] = $GetDistance->getDistance($login_loction,$supplier["loction"]);
              $supplierA1["loction"] = $supplier["loction"];
              array_push($loction_supplier_id,$supplierA1);
          }
        }
        // 遍历附近供应商id数组
        $loction_supplier_mesg = array();           //相距在10000米以内的供应商信息
        foreach ($loction_supplier_id as $loction_supplier_user_id) {
           $loc_suuer_data = Db::query("SELECT `id`,`nick_name`, `head_image_url` FROM `media_user` WHERE `id`=?",[$loction_supplier_user_id["id"]]);
           $supplierA2 = array();
           $supplierA2["id"] = $loction_supplier_user_id["id"];
           $supplierA2["distance"] = $loction_supplier_user_id["distance"];
           $supplierA2["loction"] = $loction_supplier_user_id["loction"];
           $supplierA2["nick_name"] = $loc_suuer_data[0]["nick_name"];
           $supplierA2["head_image_url"] = $loc_suuer_data[0]["head_image_url"];
           array_push($loction_supplier_mesg,$supplierA2);
        }
        // 打乱数组
        shuffle( $loction_supplier_mesg );
        for ($i= 0;$i< count($loction_supplier_mesg); $i++){ 
          // 提供商品
            $provide_count = Db::query("select count(`sort_id`) from `media_provide` WHERE `id`=? and `product_state`=?",[$loction_supplier_mesg[$i]["id"],1]);   
            $loction_supplier_mesg[$i]["provide_count"] = $provide_count[0]["count(`sort_id`)"];

            // 动态
            $dynamic_count = Db::query("select count(`id`) from `media_dynamic` WHERE `user_id`=? and `type`=? and `status`=?",[$loction_supplier_mesg[$i]["id"],1,1]);   
            $loction_supplier_mesg[$i]["dynamic_count"] = $dynamic_count[0]["count(`id`)"];
        } 

        //dump( $loction_supplier_mesg );

        //实例化Pagination模型,用于分页
        //得到当前是第几页  
        if (isset ($_GET["page"])) { //是否存在"id"的参数  
            $page = $_GET["page"];  
        } else {  
            $page = 1;  
        }  
        $Pagination = Loader::model('Pagination');
        $page_mesg = $Pagination->pagination($loction_supplier_mesg,10,$page);   //分页数据

        // 用户上次登录位置
        $page_mesg["login_loction"] = $login_loction;
        return $page_mesg;
	}
	public function randProvide()
	{
		$randProvide = Db::query("SELECT * FROM `media_provide`  AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX(sort_id) FROM  `media_provide`)) AS sort_id) AS t2 WHERE t1.sort_id >= t2.sort_id ORDER BY t1.sort_id ASC LIMIT 8");
		return $randProvide;
	}
	public function bgArry($end)
	{
		$numbers = range (1,$end); 
        //shuffle 将数组顺序随即打乱 
        shuffle ($numbers); 
        //array_slice 取该数组中的某一段 
        $num=3; 
        $bgArry = array_slice($numbers,0,$num); 

        return $bgArry;
	}
}