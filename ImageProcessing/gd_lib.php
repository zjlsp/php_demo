<?php
# @Author: Shengpeng Li
# @Date:   2018-02-01 15:40:38
# @Filename: gd_lib.php
# @Last modified by:   Shengpeng Li
# @Last modified time: 2018-03-17 08:26:37

header('Content-type:text/html;charset=utf-8');
class gd_lib
{

    // 图片路径
    private $imgPath = '';

    // 图片信息
    private $imgInfo = array();

    // 图片的最大尺寸
    private $maxHW = array('width'=>1200,'height'=>2000);

    // JPG图片质量，仅限JPG格式有效
    private $quality = 80;

    // GD裁剪图片规格
    private $norms = array();

    // 图片背景颜色  不需要携带#号
    private $bgColor = array("red"=>255, "green"=>255, "blue"=>255);

    // 版权图片路径
    private $mark = '';

    // 版权图片位置
    private $position = 9;

    // 版权透明度
    private $transparence = 90;

    // 版权图片宽度(根据背景图片的宽度，调整版权图片的宽度，仅支持百分比小数，1=100%  0.5=50%  0=版权宽度不改变)
    private $markWidt = 0;

    // 图片版权间距（单位px）
    private $margin = 10;

    // 是否覆盖原图
    private $isrecover = false;

    // 正则匹配文件名 和后缀名
    private $reg = '/([\w.]+)\.(\w+)$/';

    public function __construct()
    {
        # code...
    }

    /**
     * 获取图片路径或者设置图片路径
     * @参数 string $path 图片路径，仅支持绝对路径
     * @返回 string       图片路径
     */
    public function setFileDir($path='')
    {
        if ($path && file_exists($path)) {
            $this->imgPath = $path;
        }
        return $this->imgPath;
    }

    /**
     * 获取图片信息
     * @参数  string $picture [图片地址]
     * @返回
     */
    public function getImgInfo($path='')
    {
        // 判断文件是否存在
        if (!$path || !file_exists($path)) {
            return false;
        }
        // 获取图片的基本信息 高度/宽度/类型/图片名  E:\server\www\GD\image\2auto.jpg
        $infos = getimagesize($path);
        $info["width"] = $infos[0];
        $info["height"] = $infos[1];
        $info["type"] = $infos[2];
        // 正则匹配文件名和文件后缀
        preg_match($this->reg, $path, $matche);
        $info["name"] = $matche[1];
        $info["suffix"] = $matche[2];
        return $info;
    }

    /**
     * 判断宽高长度是否查过最大长度
     * @参数  integer $width  图片宽度
     * @参数  integer $height 图片高度
     * @返回  array           返回合法的宽高
     */
    private function isMaxWH($width=0, $height=0)
    {
        $_width = $width;   // 保留原宽度
        $_height = $height; // 保留原高度
        if ($width > $this->maxHW['width']) {  // 超过最大宽度
            $widht = $this->maxHW['width'];
            $height = floor(($widht/$_width)*$_height); // 新的高度 = (计算后宽度/原宽度)*原高度
        }
        if ($height > $this->maxHW['height']) {    // 超过最大高度
            $height = $this->maxHW['height'];
            $widht = floor(($height/$_height)*$_width);  // 新的宽度 = (计算后高度/原高度)*原宽度
        }
        return array('width' => $width, 'height' => $height);
    }

    /**
     * 设置新图片 画布宽高
     * @参数 integer $width  新图片的宽度
     * @参数 integer $height 新图片的高度
     * @返回 boolean
     */
    private function setWH($width=0, $height=0)
    {
        if (!$this->imgPath) {   // 判断图片路径是否已经录入
            return false;
        }
        if (empty($this->imgInfo)) { // 判断是否已经获取了图片信息，如果没有则重新获取
            $this->imgInfo = $this->getImgInfo($this->imgPath);
        }
        $infos = $this->imgInfo;
        if (!$width && !$height) {  // 宽度高度都自适应，使用原图宽高计算裁剪尺寸
            $wh = $this->isMaxWH($infos['width'], $infos['height']);
        } else {                      // 否则使用形参宽高计算
            $wh = $this->isMaxWH($width, $height);
        }
        $w  = $wh['width'];
        $h  = $wh['height'];
        if (!$width) {  // 宽度自适应
            $w = ($wh['height']/$infos['height'])*$infos['width'];
        } elseif (!$height) {   // 高度自适应
            $h = ($wh['width']/$infos['width'])*$infos['height'];
        }
        // 保存宽高到非静态属性（GD裁剪尺寸规格中）
        $this->norms['width'] = $w;
        $this->norms['height'] = $h;
        return true;
    }

    // 通过已知的参数，设置图片裁剪的相关信息（图片裁剪的方法，图片保存的路径，新图片名后缀）
    private function setNorms($cuttype, $path, $suffix)
    {
        $allow_cuttype = array('compress' => 1,'limited' => 2,'zoom' => 3);
        if (!$allow_cuttype[$cuttype]) {
            return false;
        }
        $this->norms['cuttype'] = $allow_cuttype[$cuttype];
        $this->norms['path']    = $path;
        $this->norms['suffix']  = $suffix;
        return true;
    }

    /**
     * 获取图片信息流
     * @参数  string $file 图片绝对路径
     * @返回  resource       图片资源流
     */
    private function _getImgFrom($path)
    {
        $info = $this->GetImgInfo($path);
        $img = "";
        if ($info["type"] == 1 && function_exists("imagecreatefromgif")) {
            $img = imagecreatefromgif($path);
            ImageAlphaBlending($img, true);
        } elseif ($info["type"] == 2 && function_exists("imagecreatefromjpeg")) {
            $img = imagecreatefromjpeg($path);
            ImageAlphaBlending($img, true);
        } elseif ($info["type"] == 3 && function_exists("imagecreatefrompng")) {
            $img = imagecreatefrompng($path);
            ImageAlphaBlending($img, true);
        }
        return $img;
    }

    /**
     * 根据已知的条件计算出新图片的相关参数 ($dst_x,$dst_y,$dst_w,$dst_h)
     * @返回 array 代表imagecopyresampled函数中的参数  x轴   y轴   新图片的宽度   新图片的高度
     */
    private function _getXYWH()
    {
        $dst_x  = 0;                            // 新图片的坐标轴X【可变】
        $dst_y  = 0;                            // 新图片的坐标轴Y【可变】
        $dst_w  = $this->norms['width'];        // 新图片压缩后的宽度【可变】
        $dst_h  = $this->norms['height'];       // 新图片压缩后的高度【可变】
        $src_w  = $this->imgInfo['width'];      // 原图片宽度
        $src_h  = $this->imgInfo['height'];     // 原图片高度
        $width  = $this->norms['width'];        // 新图片画布的宽度
        $height = $this->norms['height'];       // 新图片画布的高度

        // 原图片的宽度高度均小于画布的宽度高度   执行居中处理
        if ($src_w<$width && $src_h<$height) {
            $dst_x = ($width-$src_w)/2;
            $dst_y = ($height-$src_h)/2;
            return array('dst_x'=>$dst_x,'dst_y'=>$dst_y,'dst_w'=>$src_w,'dst_h'=>$src_h);
        }

        if ($this->norms['cuttype'] == 2) {
            // 新图片画布的宽高比 大于 原图片的宽高比
            if ($width/$height > $src_w/$src_h) {
                $dst_w = ($dst_h/$src_h)*$src_w;    // 新的宽度 = (新图片高度/原图片高度)*原图片宽度
                $dst_x = ($width-$dst_w)/2;         // 新的坐标x = (画布的宽度-新图片的宽度)/2
            } else {
                $dst_h = ($dst_w/$src_w)*$src_h;
                $dst_y = ($height-$dst_h)/2;
            }
        } elseif ($this->norms['cuttype'] == 3) {
            // 新图片画布的宽高比 大于 原图片的宽高比
            if ($width/$height > $src_w/$src_h) {
                $dst_h = ($dst_w/$src_w)*$src_h;    // 新的高度 = (新图片宽度/原图片高度)*元图片高度
                $dst_y = ($height-$dst_h)/2;        //
            } else {
                $dst_w = ($dst_h/$src_h)*$src_w;    // 新的宽度 = (新图片高度/原图片高度)*原图片宽度
                $dst_x = ($width-$dst_w)/2;         // 新的坐标x = (画布的宽度-新图片的宽度)/2
            }
        }
        return array('dst_x'=>$dst_x,'dst_y'=>$dst_y,'dst_w'=>$dst_w,'dst_h'=>$dst_h);
    }

    /**
     * 将十六进制转成RGB格式
     * @参数  string $color 十六进制颜色字符串
     * @返回  array          RGB格式数组
     */
    private function _toRGB($color="")
    {
        if (!$color) {
            return false;
        }
        if (strlen($color) != 6) {
            return false;
        }
        $color = strtolower($color);
        $array["red"] = hexdec(substr($color, 0, 2));
        $array["green"] = hexdec(substr($color, 2, 2));
        $array["blue"] = hexdec(substr($color, 4, 2));
        return $array;
    }

    /**
     * 创建画布资源
     * @参数  mixed  $width       画布宽度
     * @参数  mixed  $height      画布高度
     * @参数  mixed  $bgColor     画布背景颜色
     * @参数  integer $transparent  0-127的整形数值，0表示不背景透明，127表示背景全透明
     * @返回  resource               画布资源
     */
    public function _createImg($width, $height, $bgColor, $transparent=0)
    {
        // 如果是十六进制格式颜色 将转成RGB格式颜色
        $bgColor = is_string($bgColor) ? $this->_toRGB($bgColor) : $bgColor;
        if (!$bgColor) {    // 经过_toRGB方法计算，转换失败返回false
            return false;
        }
        $img = imagecreatetruecolor($width, $height);
        if (!$transparent) {    // 背景不透明
            $image_bg = imagecolorallocate($img, $bgColor["red"], $bgColor["green"], $bgColor["blue"]); // 分配背景颜色
            imagefill($img, 0, 0, $image_bg);    // 填充画布背景
        } else {
            $zhibg = imagecolorallocatealpha($img, 0, 0, 0, $transparent);  // 创建透明画布
            imagealphablending($img, false);   //关闭混合模式
            imagecolortransparent($img, $zhibg);
            imagefill($img, 0, 0, $zhibg);
            imagesavealpha($img, true);    //设置保存PNG时保留透明通道信息
        }
        return $img;
    }

    /**
     * 设置图片版权参数
     * @参数 string   $path        版权图片全路径
     * @参数 string  $position     版权图片的方位
     * @参数 integer $transparence 版权图片的透明度
     * @返回
     */
    public function setMark($path, $position='bottom-right', $transparence=80, $markWidt=0)
    {
        // 设置版权图片路径
        if ($path && file_exists($path)) {
            $this->mark = $path;
        } else {
            return false;
        }
        // 列出所支持的版权方位
        $allow_position = array('top-left' => 1, 'top-middle' => 2, 'top-right' => 3, 'middle-left' => 4, 'middle-middle' => 5, 'middle-right' => 6, 'bottom-left' => 7, 'bottom-middle' => 8, 'bottom-right' => 9);
        if (!$this->position = $allow_position[$position]) {
            return false;
        }
        $this->transparence = $transparence;
        $this->markWidt = $markWidt;
        return true;
    }

    /**
     * 根据已知的参数，执行水印覆盖操作
     * @参数 resource $pic 需要执行的画布资源
     * @返回 resource
     */
    private function CopyMark($pic)
    {
        // 获取版权图片信息流
        if (!$mark = $this->_getImgFrom($this->mark)) {
            return $pic;
        }
        $picW = imagesx($pic);  // 底图的宽度
        $picH = imagesy($pic);  // 底图的高度
        $markW = imagesx($mark);
        $markH = imagesy($mark);
        if ($this->markWidt) {   // 如果图片设置到需要根据百分比来添加水印
            $markW = $picW*$this->markWidt;
            $markH = ($markW/imagesx($mark))*imagesy($mark);
        }
        $watermark = $this->_createImg($markW, $markH, $this->bgColor, 127);  // 创建一个符合大小的透明水印画布
        imagecopyresampled($watermark, $mark, 0, 0, 0, 0, $markW, $markH, imagesx($mark), imagesy($mark));
        $mark = $watermark;

        // 计算出x轴的水印位置
        switch (($this->position-1)%3) {
            case 2:
                $x = $picW-$markW-$this->margin;
                break;
            case 1:
                $x = (($picW-$markW)/2)+$this->margin;
                break;
            default:
                $x = $this->margin;
                break;
        }
        // 计算出y轴的水印位置
        switch (floor(($this->position-1)/3)) {
            case 2:
                $y = $picH-$markH-$this->margin;
                break;
            case 1:
                $y = (($picH-$markH)/2)+$this->margin;
                break;
            default:
                $y = $this->margin;
                break;
        }
        imagecopymerge($pic, $mark, $x, $y, 0, 0, $markW, $markH, $this->transparence);
        return $pic;
    }

    /**
     * 保存图片资源为文件
     * @参数  resource $pic 图片资源
     * @返回  boolean
     */
    private function _write_imgto($pic)
    {
        // 获取文件后缀名
        $file_suffix = $this->imgInfo['suffix'];
        // 拼接图片保存全路径
        $dir = $this->norms['path'].$this->imgInfo['name'].$this->norms['suffix'].'.'.$file_suffix;
        // 拼接图像输出函数名
        $fn_name = 'image'.$file_suffix;
        if ($file_suffix === 'jpg' || $file_suffix === 'jpeg') {
            return imagejpeg($pic, $dir, $this->quality);
        } else {
            return $fn_name($pic, $dir);
        }
    }

    /**
     * 执行图片处理方法
     * @参数 array $flow 图片处理参数
     */
    public function setNewImg($flow)
    {
        // 如果信息流不是数组类型，不接受
        if (!is_array($flow)) {
            return false;
        }
        // 判断等待处理的图片（原图）路径是否存在
        if (!$this->imgPath) {
            return false;
        }
        // 获取图片信息流资源
        $img = $this->_getImgFrom($this->imgPath);

        foreach ($flow as $value) {
            // 判断是否存在保存路径
            if (!isset($value['path']) || !file_exists($value['path'])) {
                return false;
            }
            // 配置默认参数
            $width  = isset($value['width']) ? $value['width'] : 0;
            $height = isset($value['height']) ? $value['height'] : 0;
            $cuttype = isset($value['cuttype']) ? $value['cuttype'] : 'zoom';
            $suffix = isset($value['suffix']) ? $value['suffix'] :'';
            $bgColor = isset($value['bgColor']) ? $value['bgColor'] : $this->bgColor;
            $is_mark = isset($value['is_mark']) === null ? false : $value['is_mark'];

            // 如果后缀名为空，并且禁止覆盖原图
            if (!$suffix && !$this->isrecover) {
                return false;
            }
            // 设置新图片的宽度高度
            if (!$this->setWH($width, $height)) {
                return false;
            }
            // 设置图片流程所需的 方法/保存地址/后缀名
            if (!$this->setNorms($value['cuttype'], $value['path'], $value['suffix'])) {
                return false;
            }
            // 根据已知的条件计算出新图片的相关参数 拷贝图片放置的xy轴，放置的宽高
            $this->norms = array_merge($this->norms, $this->_getXYWH());
            // 创建新图片的画布
            $newPic = $this->_createImg($this->norms['width'], $this->norms['height'], $bgColor);
            // 根据已知的条件进行图片裁剪
            imagecopyresampled($newPic, $img, $this->norms['dst_x'], $this->norms['dst_y'], 0, 0, $this->norms['dst_w'], $this->norms['dst_h'], $this->imgInfo['width'], $this->imgInfo['height']);

            // 是否开启水印模式
            if ($is_mark) {
                // 是否存在水印必要参数
                if (isset($value['mark_info']) && isset($value['mark_info']['path'])) {
                    $mark_info = $value['mark_info'];
                    $mark_info['position'] = isset($mark_info['position']) ? $mark_info['position'] : 'bottom-right';
                    $mark_info['transparence'] = isset($mark_info['transparence'])  ? $mark_info['transparence'] : 80;
                    $mark_info['markWidt'] = isset($mark_info['markWidt'])  ? $mark_info['markWidt'] : 0;
                    $this->setMark($mark_info['path'], $mark_info['position'], $mark_info['transparence'], $mark_info['markWidt']);
                }
                // 判断是否已经设置好水印参数，或者水印参数完整
                if ($this->mark) {
                    $this->CopyMark($newPic);
                }
            }

            // 执行文件保存
            if (!$this->_write_imgto($newPic)) {
                return false;
            }
            return true;
        }
    }
}
