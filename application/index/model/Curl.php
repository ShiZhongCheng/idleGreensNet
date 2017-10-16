<?php
namespace app\index\model;

use think\Model;

class Curl extends Model
{
	public function curlGet($address)
	{
		$address_String = "";
		for ($i=0; $i < count($address); $i++) { 
			$address_String .= $address[$i];
		}
		$url = "http://restapi.amap.com/v3/geocode/geo?address=".$address_String."&output=JSON&key=e221e8b1b9e100e1536a2afc2e5fa30e&";
		//初始化一个 cURL 对象 
		$ch  = curl_init();
		//设置你需要抓取的URL
		curl_setopt($ch, CURLOPT_URL, $url);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//是否获得跳转后的页面
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		$return_data = (array)json_decode( $data );
		$return_data = (array)$return_data["geocodes"][0];
		
		// 转化为数组类型
		// for ($a= 0;$a< count($return_data["districts"]); $a++){ 
		//     $return_data["districts"][$a] = (array)$return_data["districts"][$a];
		//     for ($b=0; $b < count($return_data["districts"][$a]["districts"]); $b++) { 
		//     	$return_data["districts"][$a]["districts"][$b] = (array)$return_data["districts"][$a]["districts"][$b];
		//     	for ($c=0; $c < count($return_data["districts"][$a]["districts"][$b]["districts"]); $c++) { 
		// 	    	$return_data["districts"][$a]["districts"][$b]["districts"][$c] = (array)$return_data["districts"][$a]["districts"][$b]["districts"][$c];
		// 	    	for ($d=0; $d < count($return_data["districts"][$a]["districts"][$b]["districts"][$c]["districts"]); $d++) { 
		// 		    	$return_data["districts"][$a]["districts"][$b]["districts"][$c]["districts"][$d] = (array)$return_data["districts"][$a]["districts"][$b]["districts"][$c]["districts"][$d];
		// 		    }
		// 	    }
		//     }
	    //    } 
	    return $return_data["location"];
	}
}