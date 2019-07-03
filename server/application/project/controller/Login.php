<?php

namespace app\project\controller;

// model 模块
// use app\common\Model\Member;
// use app\common\Model\Organization;

use controller\BasicApi;

// extends 下的 service 模块
// use service\JwtService;
// use service\LogService;
// use service\NodeService;
// use service\RandomService;

// 邮箱 与 短信 公共模块
// use mail\Mail;
// use sms\Sms;

// 引入 think 内置模块
// use think\Db;
// use think\db\exception\DataNotFoundException;
// use think\db\exception\ModelNotFoundException;
// use think\exception\DbException;

// think 内置 facade 模块
use think\facade\Hook;
use think\facade\Log;
use think\facade\Request;
use think\facade\Validate;


/**
 * @title 用户相关
 * @description 接口说明
 * @group 接口分组
 */
class Login extends BasicApi
{
  // 控制器基础方法
  public function initialize(){

  }

  public function index(){
    // if($this -> request -> isGet()){
    //   return $this -> fetch('', ['title' => '用户登录']);
    // }
    // // 输入数据验证
    // $validate = Validate::make([
    //   'account' => 'require|min:4',
    //   'password'=> 'require|min:4'
    // ],[
    //   'account.require' => '登录账号不能为空',
    //   'account.min' => '登录账号不能少于4位有效字符'
    // ]);

    $data = [
      'account' => $this ->request->post('acount', ''),
      'password' => $this ->request->post('password', '')
    ];

    $validate -> check($data) || $this-> error($validate-> getError);
    $this ->request->post('mobile', '');

    // 使用手机
    if( $mobile ) {
      if (cache('captcha') != Request::param('captcha')){
        $this ->error('验证码错误', 203);
       }

    //  if(cache('captchaMobile'))
    }
  }

  /**
   * 获取验证码
   */
  public function getCaptcha(){
    $mobile = $this ->request->post('mobile', '');
    $code = RandomServie

  }
}