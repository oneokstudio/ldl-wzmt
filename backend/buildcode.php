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
require_once __DIR__ . '/PhpExcel/PHPExcel.php';

date_default_timezone_set('Asia/ShangHai');

$filePath = "codes.xlsx";

//建立reader对象
$PHPReader = new PHPExcel_Reader_Excel2007();
if(!$PHPReader->canRead($filePath)){
    $PHPReader = new PHPExcel_Reader_Excel5();
    if(!$PHPReader->canRead($filePath)){
        echo 'no Excel';
        return ;
    }
}

//建立excel对象，此时你即可以通过excel对象读取文件，也可以通过它写入文件
$PHPExcel = $PHPReader->load($filePath);

/**读取excel文件中的第一个工作表*/
$currentSheet = $PHPExcel->getSheet(0);
/**取得最大的列号*/
$allColumn = $currentSheet->getHighestColumn();
/**取得一共有多少行*/
$allRow = $currentSheet->getHighestRow();

$data = [];
// 加载字体大小
$font = new BCGFontFile('./barcodegen/font/Arial.ttf', 18);
//颜色条形码
$color_black = new BCGColor(0, 0, 0);
$color_white = new BCGColor(255, 255, 255);
$drawException = null;


//循环读取每个单元格的内容。注意行从1开始，列从A开始
for($rowIndex=2;$rowIndex<=$allRow;$rowIndex++){
    $row = [];
    for($colIndex='C';$colIndex<=$allColumn;$colIndex++){
        $addr = $colIndex.$rowIndex;
        $cell = $currentSheet->getCell($addr)->getValue();
        if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
            $cell = $cell->__toString();
        $row[] = $cell;
    }
    $data[] = $row;
}

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=wzmt', 'root', 'zxc');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach($data as $row) {
        try {
            $code = new BCGcode39();
            $code->setScale(2);
            $code->setThickness(30); // 条形码的厚度
            $code->setForegroundColor($color_black); // 条形码颜色
            $code->setBackgroundColor($color_white); // 空白间隙颜色
            $code->setFont($font); //
            $code->parse($row[0]); // 条形码需要的数据内容
        } catch(Exception $exception) {
            $drawException = $exception;
        }

        //根据以上条件绘制条形码
        $drawing = new BCGDrawing(__DIR__ . '/codes/' . $row[0] . '.png', $color_white);
        if($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->draw();
        }
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

        $stmt = $db->prepare("insert into codes(ex_code, barcode) values(:ex_code, :barcode)");
        $stmt->bindParam(':ex_code', $row[1], PDO::PARAM_STR);
        $stmt->bindParam(':barcode', $row[0], PDO::PARAM_STR);
        $stmt->execute();
    }

    $db = null;
} catch (PDOException $e) {
    echo $e->getTraceAsString();
}

echo 'finish!';


