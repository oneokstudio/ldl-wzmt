<?php
/**
 * Created by PhpStorm.
 * User: sysumeepo
 * Date: 15/10/23
 * Time: 上午10:36
 */

require_once __DIR__ . '/barcodegen/class/BCGFont.php';
require_once __DIR__ . '/barcodegen/class/BCGColor.php';
require_once __DIR__ . '/barcodegen/class/BCGDrawing.php';
require_once __DIR__ . '/barcodegen/class/BCGcode39.barcode.php';

// 加载字体大小
$font = new BCGFontFile('./barcodegen/font/Arial.ttf', 18);

//颜色条形码
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);

$drawException = null;
try {
    $code = new BCGcode39();
    $code->setScale(2);
    $code->setThickness(30); // 条形码的厚度
    $code->setForegroundColor($color_black); // 条形码颜色
    $code->setBackgroundColor($color_white); // 空白间隙颜色
    $code->setFont($font); //
    $code->parse('HELLO'); // 条形码需要的数据内容
} catch(Exception $exception) {
    $drawException = $exception;
}

//根据以上条件绘制条形码
$drawing = new BCGDrawing(__DIR__ . '/HELLO.png', $color_white);
if($drawException) {
    $drawing->drawException($drawException);
} else {
    $drawing->setBarcode($code);
    $drawing->draw();
}

// 生成PNG格式的图片
//header('Content-Type: image/png');


$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
