<?php
namespace app\api\controller;

use think\Controller;
//use think\captcha\Captcha;

class Common extends Controller{

    public function verify2()
    {
        // $captcha = new Captcha();
        // return $captcha -> entry();
    }
    // _alpha
    public function verify(){
        Header("content-type: image/PNG");
        //指定大小
        $im = imagecreate(44,18);
        //定义背景颜色画布
        $back =ImageColorAllocate($im, 245, 245, 245);
        $v_codes= "";
        srand((double)microtime()*1000000);

        //生成四位数字
        for($i = 0; $i<4; $i++)
        {
            //生成随机颜色
            $font = ImageColorAllocate($im ,rand(100,255), rand(0,100),rand(100,255));
            $authnum=rand(1,9);
            $v_codes .= $authnum;
            imagestring($im, 5, 2+$i*10, 1, $authnum, $font);
        }
        $_SESSION['v_code'] = $v_codes;
        // 加入干扰像素
        for($i =0; $i<200; $i++)
        {
            $randcolor = ImageColorallocate($im,rand(0,255),rand(0,255),rand(0,255));
            imagesetpixel($im, rand()%70 , rand()%30 , $randcolor); // 画像素点函数
        }
        ImagePNG($im);
        ImageDestroy($im);
        return;
        /*
            <image src="http://doc.com/api/common/verify_alpha" />
        */
    }
}

