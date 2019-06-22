<?php
namespace app\api\model;

use think\Model;

class UserToken extends Model
{
    // 模型初始化
    protected static function init()
    {
    //TODO:初始化内容
    }

    // 检验码 加密
    public function encryp($token){
        // $token = '1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380c1ba';
        $flag1 = substr($token , 0, 60);
        return $flag1.substr(md5( $flag1 ), 0, 4);

    }

    // 检验码 解密
    public function decryp($token){
        // $token = '1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380bd2f'

        return (substr(md5( substr($token , 0, 60) ), 0, 4) == substr($token , 60));
    }

    //创建
    public function createToken($uid, $token_expire = 0){
        // 3 个月过期的token
        $token_expire = $token_expire > 0 ? (time() + $token_expire) : (time()+60*60*24*90);
        $token = md5(md5($uid.$token_expire.time().rand()."xiethan")."rdgsvgsrgr67hghf54t").md5($uid.$token_expire.time().rand()."xiethan");
        // 加入 检验码
        $token = $this -> encryp($token);
        $data['uid'] = $uid ;
        $data['token'] = $token;
        $data['token_expire'] = $token_expire;
        $data['ip'] = getIPaddress();
        $data['addtime'] = time();
        $res = $this -> save($data);
        if($res){
            //删除过期的token 
			$this->where( "token_expire < ".time() )->delete();
			return $token ;          

        }else{
            return false;
        }
    }

    public function getToken($token)
    {
        return $this->where("token",$token) ->find() ;

    }

    public function setLastTime($token)
    {
        return $this -> save(["last_check_time" => time()],['token',$token]);
        //$user->allowField(['name','email'])->save($_POST, ['id' => 1]);
    }
}