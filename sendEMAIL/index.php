<?php
# @Author: 李圣鹏
# @Date:   2018-03-14 22:00:36
# @Filename: index.php
# @Last modified by:   李圣鹏
# @Last modified time: 2018-03-14 22:25:20

/**
 * 实现php发送邮件
 * @param  string $toEMAIL 发送到的邮箱地址
 * @param  string $title   发送邮件的标题
 * @param  string $content 发送邮件的内容
 * @return Manual          发送成功返回true 否则返回错误信息
 */
function sendEMAIL($toEMAIL, $title, $content)
{
    // 实例化
    include "./class.phpmailer.php";
    $pm = new PHPMailer();

    // 服务器相关信息
    $pm->Host = 'smtp.163.com'; // SMTP服务器
    $pm->IsSMTP(); // 设置使用SMTP服务器发送邮件
    $pm->SMTPAuth = true; // 需要SMTP身份认证
    $pm->Username = '***********@163.com'; // 登录SMTP服务器的用户名
    $pm->Password = '****'; //授权码 登录SMTP服务器的密码

    // 发件人邮箱和名字
    $pm->From = '***********@163.com';
    $pm->FromName = '发送人邮件';

    // 收件人信息
    $pm->AddAddress($toEMAIL); // 添加一个收件人


    $pm->CharSet = 'utf-8'; // 内容编码
    $pm->Subject = $title; // 邮件标题
    $pm->MsgHTML($content); // 邮件内容

    // 发送邮件
    if ($pm->Send()) {
        return true;
    } else {
        return $pm->ErrorInfo;
    }
}
