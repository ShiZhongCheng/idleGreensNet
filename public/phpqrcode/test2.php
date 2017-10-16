<?php
include('phpqrcode.php'); 

class Mosaic
{
    
    function __construct()
    {
        $this->makeqr('0401','H',7,'qr.png');
        $this->makedst("consola.ttf","0401","dr.png",'qr.png');
        $this->split("dr.png",'qr.png');
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
    function split($dst_path,$src_path)
    {
        /*
        * 图片拼接函数
        * $dst_path,$src_path 底图路径，覆盖图路径
        */
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
        switch ($dst_type) {
            case 1://GIF
                header('Content-Type: image/gif');
                imagegif($dst);
                break;
            case 2://JPG
                header('Content-Type: image/jpeg');
                imagejpeg($dst);
                break;
            case 3://PNG
                  header('Content-Type: image/png');
                imagepng($dst);
                break;
            default:
                break;
            }
        imagedestroy($dst);
        imagedestroy($src);
    }
}
new Mosaic;

