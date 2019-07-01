<?php
namespace service;

use think\facade\Log;
use think\facade\Response;
use think\facade\Session;

/**
 * 系统工具服务
 */

class ToolsService
{
  public static function corsOptionsHandler(){
    // 会话是启用的，而且存在当前会话
    if (PHP_SESSION_ACTIVE !== session_status()) {
      Session::init(config('session.'));
    }
    
    // 获取token
    $token = request() -> header('token', '');
    empty($token) && $token = request() ->　post('token', '');
    empty($token) && $token = request() ->　get('token', '');
    
  }
}