<?php

// 上传根目录
$uploaddir = '../uploads/swiper';
$name = $_FILES['file']['name'];
$uploadfile = $uploaddir . $name;
$type = strtolower(substr(strrchr($name, '.'), 1));
//获取文件类型
$typeArr = array("jpg","png","gif","zip","mp4");
if (!in_array($type, $typeArr)) {
    echo "请上传jpg,png或gif类型的图片！";
    exit;
}

// 图片保存路径
if (!file_exists($uploaddir)) {
	mkdir($uploaddir);
}
$uploaddirY = $uploaddir . '/' . date('Y');
if (!file_exists($uploaddirY)) {
	mkdir($uploaddirY);
}
$uploaddirYM = $uploaddirY . '/' . date('m');
if (!file_exists($uploaddirYM)) {
	mkdir($uploaddirYM);
}
// 最终文件保存路径
$root = $uploaddirYM . '/' . date('YmdHis')  . '.' . $type;

if (move_uploaded_file($_FILES['file']['tmp_name'], $root)) {
	$returnRoot = 'phyStatic/'.strstr( $root,'uploads');
    echo $returnRoot;
} else {
    echo ("上传失败");
}
?>