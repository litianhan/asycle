<?php
namespace Asycle\Libs;
/**
 * Date: 2017/5/6
 * Time: 12:37
 */
/**
 * 验证码工具类
 * Class Captcha
 * @package Asycle\Core\libs
 */
class Captcha{
    protected $backgroundColor = [255,255,255];
    protected $fontColor = [[45,60,80], [192,57,43], [22,160,133], [192,57,43], [142,68,173], [48,63,159], [245,124,0], [121,85,72]];
    protected $textChars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ2346789';
    public function __construct()
    {
        if( ! extension_loaded('gd')){
            throw new \RuntimeException('需要安装gd扩展库');
        };
        $this->keyPrefix = str_replace('\\','_',__CLASS__);
    }
    public function setBackgroundColor(int $r,int $g,int $b){
        $this->backgroundColor = [$r,$g,$b];
    }
    /**
     * 生成验证码图片
     * @param string $text 验证码文本
     * @param int $width 图片宽度(像素)
     * @param int $height 图片高度(像素)
     * @param int $line 干扰线条的数量
     * @param int $point 干扰斑点的数量
     * @return resource 图片资源
     */
    public function createImage(string $text,int $width,int $height,int $line,int $point){
        if($line < 0){
            $line = 0;
        }
        if($point < 0){
            $point = 0;
        }
        $img = $this->newCanvas($width,$height,$this->backgroundColor);
        //添加模糊背景
        $pointColor = imagecolorallocate($img,220,220,220);
        $r = $width/($point+1);
        for($p = 0;$p < $point;$p++){
            $cx = mt_rand(0,$width);
            $cy = mt_rand(0,$height);
            imagefilledellipse($img,$cx,$cy,$r,$r,$pointColor);
        }
        if($point > 0){
            imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
        }
        //绘制文本
        $chars = str_split($text);
        $count = count($chars);
        if($count <= 0){
            return $img;
        }
        $padding = $width*5/100;
        $fontWidth = ($width-$padding) / $count;
        $marginTop= $fontWidth + ($height - $fontWidth)/2;
        $fontColorArr = [];
        $i = 0;
        foreach ($chars as $char){
            $marginLeft = $i * $fontWidth+$padding;
            $angle = mt_rand(-15,15);
            $color = $this->fontColor[array_rand($this->fontColor)];
            $fontColor = imagecolorallocate($img,$color[0],$color[1],$color[2]);
            $fontColorArr[]=$fontColor;
            $fontFile = __DIR__.'/Open_Sans_regular.ttf';
            imagettftext($img,$fontWidth,$angle,$marginLeft,$marginTop,$fontColor,$fontFile,$char);
            ++$i;
        }
        //添加随机线条干扰
        for ($k=0;$k<$line;$k++){
            $x1 = mt_rand(0,$width);
            $y1 = mt_rand(0,$height);
            $x2 = mt_rand(0,$width);;
            $y2 = mt_rand(0,$height);
            $fontColor = $fontColorArr[array_rand($fontColorArr)];
            $this->imageLineThick($img,$x1,$y1,$x2,$y2,$fontColor,2);
        }
        return $img;
    }
    protected function imageLineThick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
    {
        /* 下面两行只在线段直角相交时好使
        imagesetthickness($image, $thick);
        return imageline($image, $x1, $y1, $x2, $y2, $color);
        */
        if ($thick == 1) {
            return imageline($image, $x1, $y1, $x2, $y2, $color);
        }
        $t = $thick / 2 - 0.5;
        if ($x1 == $x2 || $y1 == $y2) {
            return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
        }
        $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
        $a = $t / sqrt(1 + pow($k, 2));
        $points = array(
            round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
            round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
            round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
            round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
        );
        imagefilledpolygon($image, $points, 4, $color);
        return imagepolygon($image, $points, 4, $color);
    }
    public function blur($img,$width,$height,$pointCount = 0){
        $x1 = mt_rand(0,$width);
        $y1 = mt_rand(0,$height);
        for ($i=0;$i<$pointCount;$i++){
            $this->imageLineThick($img,$x1,$y1,$x1+mt_rand(5,10),$y1+mt_rand(5,10),imagecolorallocate($img,0,0,0),5);
        }
        imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
    }
    public function randomText(int $length ,$source = null){
        if(is_null($source)){
            $characters = str_split($this->textChars);
        }else{
            $characters = str_split($source);
        }

        $text = '';
        for($i=0; $i < $length ;$i++){
            $text .= $characters[array_rand($characters)];
        }
        return $text;
    }
    protected function randomColor(){
        $color = [mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)];
        return $color;
    }
    protected function newCanvas(int $width,int $height,array $background){
        $img = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($img,$background[0],$background[1],$background[2]);
        imagefill($img, 0, 0,$backgroundColor);
        return $img;
    }
}