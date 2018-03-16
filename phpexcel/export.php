<?php
# @Author: Shengpeng Li
# @Date:   2018-03-16 11:30:48
# @Filename: export.php
# @Last modified by:   Shengpeng Li
# @Last modified time: 2018-03-16 15:19:38

require "./Classes/PHPExcel.php"; // 引入核心文件
require "./Classes/PHPExcel/Writer/Excel5.php"; //此类主要往excel表中写数据的文件
$objPHPExcel = new PHPExcel(); // 实例一个excel核心类
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); // 将excel类对象作为参数传入进去

$sheets=$objPHPExcel->getActiveSheet()->setTitle('地区信息');//设置表格内部sheet名称

//设置sheet列头信息
$objPHPExcel->setActiveSheetIndex()
    ->setCellValue('A1', '地区ID')
    ->setCellValue('B1', '地区名称')
    ->setCellValue('C1', '父级地区ID')
    ->setCellValue('D1', '地区级别')
    ->setCellValue('E1', '邮政编码')
    ->setCellValue('F1', '地名拼音')
    ->setCellValue('G1', '拼音简写');

// PDO查询出数据
$pdo = new PDO("mysql:host=localhost;port=3306;charset=utf8;dbname=testshop", 'root', 'root');
$sql = 'SELECT * FROM t_sys_region WHERE 1';
$result = $pdo->query($sql);
$data = $result->fetchAll();

$i = 2;
foreach ($data as $v) {
    //设置单元格的值
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $v['region_id']);
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $v['region_name']);
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $v['parent_id']);
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $v['level']);
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $v['post_code']);
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $v['pinyin_name']);
    $sheets=$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $v['pinyin_short_name']);
    $i++;
}

//整体设置字体和字体大小
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');//整体设置字体
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);//整体设置字体大小


// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true); //单元格宽度自适应
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); //设置列宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10); //设置列宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10); //设置列宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10); //设置列宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10); //设置列宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10); //设置列宽度

// 输出Excel表格到浏览器下载
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="地区数据'.date('Ymd').'.xls"'); //excel表格名称
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
$objWriter->save('php://output');
