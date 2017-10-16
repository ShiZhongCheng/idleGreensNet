<?php

// 上传根目录
$uploaddir = '../uploads/course';
$name = $_FILES['file']['name'];
$uploadfile = $uploaddir . $name;
$type = strtolower(substr(strrchr($name, '.'), 1));
//获取文件类型
$typeArr = array("jpg","png","mp4","flv");
if (!in_array($type, $typeArr)) {
    echo "上传的文件格式不正确，请重新上传！";
    exit;
}

if (!file_exists($uploaddir)) {
	mkdir($uploaddir);
}

// 上传的是MP4时
if ($type == "mp4") {
	$uploaddirVideo = $uploaddir . '/video';
	if (!file_exists($uploaddirVideo)) {
		mkdir($uploaddirVideo);
	}

	if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaddirVideo . '/' . $_FILES['file']['name'])) {
		$returnRoot = 'phystatic/'.strstr( $uploaddirVideo,'uploads') . '/' . $_FILES['file']['name'];
	    echo $returnRoot;
	} else {
	    echo ("上传失败");
	}
	exit();
}

$uploaddirImg = $uploaddir . '/images';
if (!file_exists($uploaddirY)) {
	mkdir($uploaddirY);
}

// 最终文件保存路径
$root = $uploaddirImg . '/' . date('YmdHis')  . '.' . $type;

if (move_uploaded_file($_FILES['file']['tmp_name'], $root)) {
	$returnRoot = 'phystatic/'.strstr( $root,'uploads');
    echo $returnRoot;
} else {
    echo ("上传失败");
}
?>