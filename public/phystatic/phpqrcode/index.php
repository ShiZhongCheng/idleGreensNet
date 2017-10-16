<?php
include('phpqrcode.php'); 

class Mosaic
{
    
    function __construct($ex,$numbers)
    {
        // 确保数量小于100，避免出现不可控错误
        if ($numbers >= 100) {
            $numbers = 99;
        }
        // 循环生成指定数量图片
        for ($i=0; $i < $numbers; $i++) { 
            $index = str_pad($i+1,2,"0",STR_PAD_LEFT);
            // 拼接二维码内容
            $content = $ex.$index;
            $this->makeqr($content,'H',7,'data/exchange/qr.png');
            $this->makedst("font/consola.ttf",$content,"data/exchange/dr.png",'data/exchange/qr.png');
            $this->split("data/exchange/dr.png",'data/exchange/qr.png',$ex,$content);
        }
    }
    function makeqr($url,$errorCorrectionLevel,$matrixPointSize,$qrPath)
    {
        /*
        * 生成二维码图片 
        * $url 二维码内容
        * $errorCorrectionLevel = 'H' 容错级别 
        * $matrixPointSize = 7 生成图片大小 
        * $qrPath = "qr.png" 生成图片保存路径
        */
        QRcode::png($url, $qrPath, $errorCorrectionLevel, $matrixPointSize, 2); 
    }
    function makedst($fontPath,$text,$dstPth,$qrPath)
    {
        /*
        * 生成文字底图
        * $qrPath 目标图（覆盖图）路径
        * $fontPath,$text,$dstPth 字体路径，文本内容，底图保存路径
        */
        $text = "HDU".$text;
        list($with,$height,$dst_type) = getimagesize($qrPath);
        $height = $height + 10 + 5;
        $im = imagecreatetruecolor($with, $height);
        $write = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
        $black = imagecolorallocate($im, 0x00, 0x00, 0x00);
        imagefilledrectangle($im, 0, 0, $with, $height, $write);
        imagefttext($im, 10, 0, $with/2-20, $height-5, $black, $fontPath, $text);
        imagepng($im,$dstPth);
        imagedestroy($im);
    }
    function split($dst_path,$src_path,$dirname,$content)
    {
        /*
        * 图片拼接函数
        * $dst_path,$src_path 底图路径，覆盖图路径
        * $content = 0401 $dirname = 04 
        */
        // 判断文件夹是否存在和拼接文件保存路径
        $dir = "data/images/" . $dirname;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $savePath = $dir . '/' . $content .'.png';

        $dst = imagecreatefromstring(file_get_contents($dst_path));
        $src = imagecreatefromstring(file_get_contents($src_path));
        //获取覆盖图图片的宽高
        list($src_w, $src_h) = getimagesize($src_path);
        //将覆盖图复制到目标图片上，最后个参数100是设置透明度（100是不透明），这里实现不透明效果，两个20是覆盖图坐标位置
        imagecopymerge($dst, $src, 0, 0, 0, 0, $src_w, $src_h, 100);
        //如果覆盖图图片本身带透明色，则使用imagecopy方法
        //imagecopy($dst, $src, 10, 10, 0, 0, $src_w, $src_h);
        //输出图片
        list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);

        // 加黑色边框边框
        $border = imagecreatetruecolor($dst_w+2, $dst_h+2);
        $black = imagecolorallocate($border, 0x00, 0x00, 0x00);
        imagefilledrectangle($border, 0, 0, $dst_w+2, $dst_h+2, $black);
        imagecopymerge($border, $dst, 1, 1, 0, 0, $dst_w, $dst_h, 100);
        switch ($dst_type) {
            case 1://GIF
                imagegif($border,$savePath);
                // echo "<img src='".$savePath."'>";
                break;
            case 2://JPG
                imagejpeg($border,$savePath);
                // echo "<img src='".$savePath."'>";
                break;
            case 3://PNG
                // header('Content-Type: image/png');
                imagepng($border,$savePath);
                // echo "<img src='".$savePath."'>";
                break;
            default:
                break;
            }
        imagedestroy($dst);
        imagedestroy($src);
        imagedestroy($border);
    }
}

// 将文件夹压缩
function addFileToZip($path,$zip){
    $handler=opendir($path); //打开当前文件夹由$path指定。
    while(($filename=readdir($handler))!==false){
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                addFileToZip($path."/".$filename, $zip);
            }else{ //将文件加入zip对象
                $zip->addFile($path."/".$filename);
            }
        }
    }
    @closedir($path);
}

// 获取实验id
$ex = $_GET["ex"];
if ($ex >= 100) {
    echo "实验编号超过限制！";
    exit();
}
// $ex 变量填补成两位字符
$ex = str_pad($ex,2,"0",STR_PAD_LEFT);
// 获取生成多少张图片
$numbers = $_GET["numbers"];
// 获取根目录
$root = $_GET["root"];

// 压缩文件不存在时生成图片在压缩，存在重新压缩
if (!file_exists("data/zip/".$ex.'.zip')) {
    // 实例化类
    new Mosaic($ex,$numbers);
}

$zip=new ZipArchive();
if($zip->open('data/zip/'.$ex.'.zip', ZipArchive::CREATE) === TRUE){
    addFileToZip('data/images/'.$ex, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
    $zip->close(); //关闭处理的zip文件
}

// 跳转到下载页面
header("Location: ".$root."/phystatic/phpqrcode/data/zip/".$ex.".zip");
