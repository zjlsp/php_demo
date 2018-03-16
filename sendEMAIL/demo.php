<?php
// 实例化
include "class.phpmailer.php";
$pm = new PHPMailer();

// 服务器相关信息
$pm->Host = 'smtp.163.com'; // SMTP服务器
$pm->IsSMTP(); // 设置使用SMTP服务器发送邮件
$pm->SMTPAuth = true; // 需要SMTP身份认证
$pm->Username = '***********@163.com'; // 登录SMTP服务器的用户名
$pm->Password = '****'; //授权码 登录SMTP服务器的密码

// 发件人邮箱和名字
$pm->From = '***********@163.com';
$pm->FromName = '李圣鹏';

// 收件人信息
$pm->AddAddress('2597887094@qq.com'); // 添加一个收件人
//$pm->AddAddress('wangwei2@itcast.cn'); // 添加另一个收件人


$pm->CharSet = 'utf-8'; // 内容编码
$pm->Subject = '我是邮件标题'; // 邮件标题
$pm->MsgHTML('邮件内容'); // 邮件内容

// 发送邮件
if($pm->Send()){
   echo 'ok';
}else {
   echo $pm->ErrorInfo;
}
