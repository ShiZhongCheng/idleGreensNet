<?php
namespace app\index\model;

use think\Model;

class GetDistance extends Model
{
	function getDistance($loc1, $loc2) 
	{ 
	// 拆分字符串
	$loc1 = explode(',', $loc1);
	$lat1 = $loc1[0];
	$lng1 = $loc1[1];
	$loc2 = explode(',', $loc2);
	$lat2 = $loc2[0];
	$lng2 = $loc2[1];
	$earthRadius = 6367000; //approximate radius of earth in meters 
	 
	/* 
	Convert these degrees to radians 
	to work with the formula 
	*/
	 
	$lat1 = ($lat1 * pi() ) / 180; 
	$lng1 = ($lng1 * pi() ) / 180; 
	 
	$lat2 = ($lat2 * pi() ) / 180; 
	$lng2 = ($lng2 * pi() ) / 180; 
	 
	/* 
	Using the 
	Haversine formula 
	 
	http://en.wikipedia.org/wiki/Haversine_formula 
	 
	calculate the distance 
	*/
	 
	$calcLongitude = $lng2 - $lng1; 
	$calcLatitude = $lat2 - $lat1; 
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2); 
	$stepTwo = 2 * asin(min(1, sqrt($stepOne))); 
	$calculatedDistance = $earthRadius * $stepTwo; 
	 
	return round($calculatedDistance); 
	} 
}