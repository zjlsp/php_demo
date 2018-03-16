<?php
# @Author: Shengpeng Li
# @Date:   2018-03-16 15:20:38
# @Filename: import.php
# @Last modified by:   Shengpeng Li
# @Last modified time: 2018-03-16 15:50:39

require "./Classes/PHPExcel/IOFactory.php";
$excelio=PHPExcel_IOFactory::load($_FILES['excel']['tmp_name']);
$sheetcount=$excelio->getSheetCount();

$datas=$excelio->getSheet(0)->toArray(); // 获取第一个sheet数据
unset($datas[0]);  // 删除表头名字，

// PDO录入数据
$pdo = new PDO("mysql:host=localhost;port=3306;charset=utf8;dbname=testshop", 'root', 'root');
foreach ($datas as $key => $value) {
    $time = date('Y-m-d H:i:s',time());
    $sql = "insert into t_sys_region (region_id,region_name,level,parent_id,post_code,pinyin_name,pinyin_short_name,create_time,update_time) values('{$value[0]}','{$value[1]}','{$value[3]}','{$value[2]}','{$value[4]}','{$value[5]}','{$value[6]}','{$time}','{$time}')";
    $pdo->query($sql);
}
echo '成功';
