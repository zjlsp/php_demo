### 邮件发送案例代码

> 邮件发送基于第三方插件包 `phpmailer`
> https://github.com/PHPMailer/PHPMailer

#### 基本使用方法：

引入index.php文件中的`sendEMAIL`函数，直接调用使用即可

```php
sendEMAIL('收件人邮箱地址', '发送邮件标题', '发送邮件内容');
```

#### 错误问题记录：
>The following From address failed: ***********@163.com
SMTP server error: authentication is required,163 smtp12,EMCowAAHBN7gL6laBeWhEQ--.29829S3 1521037280

邮件无法正常发送且返回该报错信息，请检查授权码是否配置正确！
