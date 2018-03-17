## GD图片处理操作类文件

> 功能强大的GD图片处理的库，可以批量生成不同规格的图片尺寸，支持添加版权图片，并且支持将图片保存到指定的文件夹内

#### 类属性列表

> 图片路径 `private $imgPath`

> 图片信息 `private $imgInfo`

> 图片的最大尺寸 `private $maxHW`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`array('width'=>1200,'height'=>2000);`

> JPG图片质量 `private $quality`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`80`

> 当前处理的图片规格 `private $norms`

> 图片背景RGB颜色 `private $bgColor`

> 版权图片路径 `private $mark`

> 版权图片位置 `private $position`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`9`&nbsp;&nbsp;&nbsp;&nbsp;版权图片在右下角

> 版权透明度 `private $transparence`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`90`

> 版权图片宽度 `private $markWidt`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`0`&nbsp;&nbsp;&nbsp;&nbsp;范围区间：1~0

> - 根据背景图片的宽度，调整版权图片的宽度，仅支持百分比小数，1=100%  0.5=50%  0=版权宽度不改变

> 图片版权间距（单位px） `private $margin`

> 是否覆盖原图 `private $isrecover`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`false`

> 正则匹配文件名和后缀名 `private $reg`&nbsp;&nbsp;&nbsp;&nbsp;默认值：`/([\w.]+)\.(\w+)$/`

#### 类方法列表 `public`

> 获取图片路径或者设置图片路径 `setFileDir($path='')`

> 获取图片信息 `getImgInfo($path='')`

> 创建画布资源 `_createImg($width, $height, $bgColor, $transparent=0)`

> 设置图片版权参数 `setMark($path, $position='bottom-right', $transparence=80, $markWidt=0)`

> 执行图片处理方法 `setNewImg($flow)`

#### 类方法列表 `private`

> 设置新图片画布宽高 `setWH($width=0, $height=0)`
- 设置的最高宽度高度受到`$maxHW`属性影响,不可操作该属性宽度高度最大值  如果宽度高度为0 则为自适应最大程度保持原图宽度高度

> 通过已知的参数，设置图片裁剪的相关信息 `setNorms($cuttype, $path, $suffix)`
- 图片裁剪的方法，图片保存的路径，新图片名后缀

> 获取图片信息流 `_getImgFrom($path)`
- 目前仅支持jpeg/png/gif的图片，所以图片的GD图片处理库目前只能处理jpeg/png/gif的图片文件

> 根据已知的条件计算出新图片的相关参数 `_getXYWH()`
- 返回 array 代表imagecopyresampled函数中的参数  x轴   y轴   新图片的宽度   新图片的高度
- 必须提前使用`setNorms`和`_getImgFrom`方法获取计算出图片的相关参数，否则该该函数将无法使用

> 创建画布资源 `_createImg($width, $height, $bgColor, $transparent=0)`
- `$transparent`&nbsp;&nbsp;&nbsp;&nbsp;参数取值范围为 1~127

> 根据已知的参数，执行水印覆盖操作 `CopyMark($pic)`

> 保存图片资源为文件 `_write_imgto($pic)`

***

### GD图片处理的基础使用方法
```php
// 实例化gd库
$gd = new gd_lib();
// 添加处理的图片原图
$gd->setFileDir('./image/4.jpg');
// 定义图片处理的规格
$spec = array(
    array(
        'width' => 300,
        'height' => 300,
        'cuttype' => 'zoom',
        'suffix' => '_zoom',
        'path' => 'G:/www/demo/gd/image/',
        'bgColor' => 'fffff'
    ),
    array(
        'width' => 600,
        'height' => 900,
        'cuttype' => 'compress',
        'suffix' => '_compress',
        'path' => 'G:/www/demo/gd/image/',
        'bgColor' => 'ffffff'
    )
);
// 执行水印图片处理
$gd->setNewImg($spec);
```
#### 图片规格处理参数说明

> `width` 新图片的宽度

> `height` 新图片的高度

> `cuttype` 图片处理模式
- `compress` 根据图片高度宽度压缩图片，如果新图片的高度宽度比例和原图宽度高度比例不一致，可能会导致图片变形
- `limited` 无压缩完整展现图片内容 如果新图片的高度宽度比例和原图宽度高度比例不一致，可能会导致周围存在留白现象
- `zoom` 无压缩尽可能的完整展现图片内容【推荐模式/默认模式】

> `suffix` 新文件名添加的后缀
- 例子：`'suffix' => '_zoom',` 原图片名`imgName.jpg`，新图片保存后名字为 `imgName_zoom.jpg`,如果留空则会判断是否需要覆盖原图，通过`$isrecover`属性控制

> `path` 新图片保存路径(必填参数)

> `bgColor` 新图片的背景颜色(不需要携带#号,必须为十六进制格式颜色)

***

#### 相同版权水印添加处理
```php
// 实例化gd库
$gd = new gd_lib();
// 添加处理的图片原图
$gd->setFileDir('./image/4.jpg');
// 设置水印图片参数（共用）
$obj_gd->setMark('./image/logo.png','bottom-right',60,0.3);
// 定义图片处理的规格
$spec = array(
    array(
        # …… 忽略了其他规格参数
        'is_mark' => true
    )
);
// 执行水印图片处理
$gd->setNewImg($spec);
```
#### 版权处理参数说明
```php
$gd->setMark($path, $position, $transparence, $markWidt);
```
> `path` 版权图片路径

> `position` 版权添加位置，类库可选9个方位 默认`bottom-right`
- `top-left`&nbsp;&nbsp;&nbsp;&nbsp;左上角
- `top-middle`&nbsp;&nbsp;&nbsp;&nbsp;中上
- `top-right`&nbsp;&nbsp;&nbsp;&nbsp;右上角
- `middle-left`&nbsp;&nbsp;&nbsp;&nbsp;左中
- `middle-middle`&nbsp;&nbsp;&nbsp;&nbsp;中间
- `middle-right`&nbsp;&nbsp;&nbsp;&nbsp;右中
- `bottom-left`&nbsp;&nbsp;&nbsp;&nbsp;左下角
- `bottom-middle`&nbsp;&nbsp;&nbsp;&nbsp;中下
- `bottom-right`&nbsp;&nbsp;&nbsp;&nbsp;右下角

> `transparence` 版权图片透明度 取值范围 100~0

> `markWidt` 根据背景图片的宽度，调整版权图片的宽度，仅支持百分比小数，1=100% 0.5=50% 0=版权宽度不改变

#### 警告：
> 定义图片处理的规格参数数组中需要将`is_mark`参数设置为`true`,才会为该规格添加版权(可参考上方使用方法的代码段)

***

#### 给不同图片规格添加不同的版权图片
> 仅需要将版权处理参数添加到图片处理的规格数组中的`mark_info`属性来完成

```php
$arrayName = array(
    array(
        'width' => 600,
        'height' => 300,
        'cuttype' => 'zoom',
        'suffix' => 'zoom',
        'path' => 'G:/www/demo/gd/image/',
        'bgColor' => '147c26',
        'is_mark' => true,
        'mark_info' => array(
            'path' => './image/logo.png',
            'position' => 'bottom-right',
            'transparence' => 60,
            'markWidt' => 0.5
        )
    )
);
```
