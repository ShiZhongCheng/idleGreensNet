<?php
namespace app\index\model;

use think\Model;

class Singlesendsms extends Model
{
	public function sendMesg($ParamString,$RecNum,$SignName,$TemplateCode)
	{
		$host = "http://sms.market.alicloudapi.com";
	    $path = "/singleSendSms";
	    $method = "GET";
	    $appcode = "943ea75924174338a2914d1b2c075152";
	    $headers = array();
	    array_push($headers, "Authorization:APPCODE " . $appcode);
	    $querys = "ParamString=".$ParamString."&RecNum=".$RecNum."&SignName=".$SignName."&TemplateCode=".$TemplateCode;
	    $bodys = "";
	    $url = $host . $path . "?" . $querys;

	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($curl, CURLOPT_FAILONERROR, false);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    if (1 == strpos("$".$host, "https://"))
	    {
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    }
	    curl_exec($curl) ;
	}
}