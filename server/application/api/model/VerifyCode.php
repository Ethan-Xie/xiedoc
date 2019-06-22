<?php
namespace app\api\model;

use think\Model;
use think\facade\Cache;

class VerifyCode extends Model
{
    /**
     * 次数加一
     */
    public function ini_times($key){
        Cache::inc($key); //自增
    }

    // 查询是否超过三次
    public function is_check_times($keys, $max_times = 5)
    {
        $times = Cache::get($keys);
        if($times === false)
        {
            //初始化
            Cache::set($keys, 0, 3600);
            return true;
        }else if($times >= $max_times){
            return false;
        }else{
            return true;
        }
    }
    
}