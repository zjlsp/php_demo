# excel文件导入导出数据库

> excel文件导入导出数据库基于第三方插件包 `PHPExcel`
> https://github.com/PHPOffice/PHPExcel

##警告
PHPExcel最新版本1.8.1于2015年发布。该项目不再维护，不应再使用。

如果需要更高需求可移步`PhpSpreadsheet`
> https://github.com/PHPOffice/PhpSpreadsheet

## 目录结构

excel文件导入导出数据库：
~~~
目录结构（或者子目录）
├─phpexcel              excel文件导入导出数据库
│  ├─Classes            核心插件库
│  │  ├─PHPExcel        插件包
│  │  ├─PHPExcel.php    插件入口文件
│  ├─export.html        导出展示文件
│  ├─import.html        导入展示文件
│  ├─export.php         导出数据库文件
│  ├─export.php         导入excel文件
│  ├─district.sql       提供测试的sql数据
~~~
