<?php
namespace app\index\model;

use think\Model;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;
use think\Session;

class Register extends Model
{
	public function receiveData()
	{
		$request = Request::instance();
		$nickNmae = $request->post("nickNmae");
		$phoneNumber = $request->post("phoneNumber");
		$captchaCode = $request->post("captchaCode");

		$Register = Loader::model('Register');
		return $Register->checkMesg( $nickNmae,$phoneNumber,$captchaCode );
	}
	public function checkMesg( $nickNmae,$phoneNumber,$captchaCode )
	{
		// session初始化
        session_start();
        $captcha_code =  $_SESSION['captcha_code'];
        $returnData = array();

        // 验证不正确时
        if ( $captcha_code != $captchaCode) {
        	$returnData["isTrueCaptcha"] = "no";
        	return $returnData;
        	exit();
        }

		$countNK = Db::query("select count(`id`) from `media_user` WHERE `nick_name`=?",[$nickNmae]);
		$dataCountNK = $countNK[0]["count(`id`)"];   

		$countPH = Db::query("select count(`id`) from `media_user` WHERE `phone_number`=?",[$phoneNumber]);
		$dataCountPH = $countPH[0]["count(`id`)"];

		if ($dataCountNK != 0) 
		{
			$returnData["haveNick"] = "yes";
		}else{
			$returnData["haveNick"] = "no";
		}

		if ($dataCountPH != 0) 
		{
			$returnData["havePhone"] = "yes";
		}else{
			$returnData["havePhone"] = "no";
		}

		$returnData["isTrueCaptcha"] = "yes";
		return $returnData;
	}
	// 向手机号码发送验证码
	public function sendCaptchaToPhone()
	{
		$request = Request::instance();
		$sendPhone = $request->get("sendPhone");

		$randStr = str_shuffle('1234567890');
		$rand = substr($randStr,0,6);
		Session::set('SMS_Code',$rand);

		// 发送短信
        $SignName = "闲菜网";
        $TemplateCode = "SMS_96495060";
        $ParamString = "{'code':'".$rand."'}";
        $Singlesendsms = Loader::model('Singlesendsms');
        $mesg = $Singlesendsms->sendMesg($ParamString,$sendPhone,$SignName,$TemplateCode);

		return $sendPhone;
	}
	// 手机号码验证
	public function register()
	{
		$request = Request::instance();
		$phoneReceiveCode = $request->post("phoneReceiveCode");
		$nickNmae = $request->post("nickNmae");
		$phoneNumber = $request->post("phoneNumber");
		$captchaCode = $request->post("captchaCode");
		$password = $request->post("password");
		$checked = $request->post("checked");

		// 手机验证码不正确
		$SMS_Code = Session::get('SMS_Code');
		if ($SMS_Code != $phoneReceiveCode) {
			return "no";
		}

		$password = md5( $password );
        Db::query("INSERT INTO `media_user`(`password`, `phone_number`, `nick_name`) VALUES (?,?,?)",[$password,$phoneNumber,$nickNmae]);

        $renterData = Db::query("SELECT `id`, `nick_name`, `password` FROM `media_user` WHERE `phone_number`=?",[$phoneNumber]);
        if (!empty( $renterData[0] )) {
        	return $renterData[0];
        }
	}
}