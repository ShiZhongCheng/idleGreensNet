<?php
namespace app\index\model;

use think\Model;

class Pagination extends Model
{
	public function pagination($data,$each_disNums,$page)
	{
		 // $data,需要分页的数据
		 // $each_disNums,每页显示的数据
		 // $page,当前页
		$returnArry = array();         //返回数组
		$dataCount = count($data);     //数据数组的长度
		$pageNums = ceil( $dataCount/$each_disNums );     //数据总共可以分页
		//如果当前页大于总页数时，返回最后一页数据
		if ($page > $pageNums) {  
			$page = $pageNums;  
		}
		$startIndent = ($page-1)*$each_disNums;           //当前页数据开始下标

		//数据为空时返回空数组
		if ($dataCount == 0) {
			// 返回数组前两数据，用于上一页和下一页
			array_push($returnArry,0,0,0,0,1);      
			return $returnArry;       
		}

		// 正常数据
		$lastPage = ($page-1)>0 ? ($page-1) : (1);                             //上一页
		$nextPage = ($page+1)<=$pageNums ? ($page+1) : ($pageNums);            //下一页
		$allData  = $dataCount;                                                //总数据
		$allPageNumber = $pageNums;                                            //总共可分页数
		$nowPage = $page;                                                      //当前页

		array_push($returnArry,$lastPage,$nextPage,$allData,$allPageNumber,$nowPage);
		for($i=$startIndent,$t=0;$i < $dataCount && $t<$each_disNums; $i++,$t++){
			array_push($returnArry,$data[ $i ]);
		}
		return $returnArry;
	}
}