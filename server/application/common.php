<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function is_log(){

}

//获取 IP
function getIPaddress(){
  $IPaddress ='';
  if(isset($_SERVER)){
    // 代理 ->  HTTP_X_FORWARDED_FOR -> HTTP_CLIENT_IP -> REMOTE_ADDR
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
      $IPaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
      $IPaddress = $_SERVER['HTTP_CLIENT_IP'];
    }else{
      $IPaddress = $_SERVER['REMOTE_ADDR'];
    }
  }else{
        // 代理 ->  HTTP_X_FORWARDED_FOR -> HTTP_CLIENT_IP -> REMOTE_ADDR
        if(isset($getenv['HTTP_X_FORWARDED_FOR']))
        {
          $IPaddress = $getenv['HTTP_X_FORWARDED_FOR'];
        }else if(isset($getenv['HTTP_CLIENT_IP'])){
          $IPaddress = $getenv['HTTP_CLIENT_IP'];
        }else{
          $IPaddress = $getenv['REMOTE_ADDR'];
        }
  }
  return $IPaddress;
}



/**
 * 获得当前的域名
 *
 * @return  string
 */
function get_domain()
{
    /* 协议 */
    $protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
    {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    }
    elseif (isset($_SERVER['HTTP_HOST']))
    {
        $host = $_SERVER['HTTP_HOST'];
    }
    else
    {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT']))
        {
            $port = ':' . $_SERVER['SERVER_PORT'];

            if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
            {
                $port = '';
            }
        }
        else
        {
            $port = '';
        }

        if (isset($_SERVER['SERVER_NAME']))
        {
            $host = $_SERVER['SERVER_NAME'] . $port;
        }
        elseif (isset($_SERVER['SERVER_ADDR']))
        {
            $host = $_SERVER['SERVER_ADDR'] . $port;
        }
    }

    return $protocol . $host;
}