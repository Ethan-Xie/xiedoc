<?php
namespace app\api\controller;


use app\api\model\Options;
use app\api\model\VerifyCode;
use think\request;
use think\controller;
use think\Db;
class User extends Base{
    //Request $request 依赖注入
    //用户注册接口
    public function register(Request $request){
        $username = trim($request->param('username'));
        $password = input('param.password');
        $confirm_password = input('param.confirm_password');
        $v_code = input('param.v_code');

        // 注册功能是否打开
        $Options = new Options();
        $register_open = $Options  -> get_value("register_open");
        //var_dump($register_open);
        if($register_open === '0')
        {
            $this -> sendError(101011, "管理员已关闭注册");
            return ;
        }
        //DEBUG
        var_dump(config('CloseVerify'));exit;
        if(!config('CloseVerify') &&  $v_code && $v_code == session('v_code') ){
           session('v_code', null);
           //var_dump($password,$confirm_password);
           if($password != '' && $password == $confirm_password){
               if($username && !model("user") -> isExist($username))
               {
                   $new_uid = model("user") -> register($username, $password);
                   //DEBUG
                   if($new_uid)
                   {
                       // 给出登录提示
                       $this->sendResult(array()); 
                       return;
                       //设置自动登录(去除)
                            $ret = model("user") -> where(['uid' => $new_uid]) -> find();
                            unset($ret['password']);
                            // session
                            session("login_user" , $ret );
                            $token = model("UserToken")->createToken($ret['uid']);
                            cookie('cookie_token',$token,60*60*24*90);//此处由服务端控制token是否过期，所以cookies过期时间设置多久都无所谓
                            $this->sendResult(array('token' => $token)); 
                   }else{
                    $this->sendError(101012,'register fail2');
                   }
               }else{
                $this->sendError(101013,lang('register fail'));
               }
           }else{
            $this->sendError(101014,lang('code_much_the_same'));
            }
           }else{
            $this->sendError(10206,lang('verification_code_are_incorrect'));
            }
        
    }
    // 登录
    /**
     * 测试 showdoc1 1
     */
    public function login(Request $request){
        $username = trim($request->param('username'));
        $password = input('param.password');// 这个写法和上面一样
        $v_code = input('param.v_code');

        // 检查用户输错密码错误次数，如果超过一定次数，这需要验证码
        $key = 'login_fail_times_'.$username;
        // var_dump(model("VerifyCode") -> is_check_times($key));
        // 判断是否过次数 
        if( !model("VerifyCode") -> is_check_times($key)){
            if(empty(trim($v_code))){
                $this->sendError(10211, '请输入验证码');
                return;
            }else if($v_code != session('v_code')){
                $this->sendError(10206, '请输入正确的验证码');
                // 数量加1 后加
                return;
            }
        }
        
        // 检验
        $res = model("User")->checkLogin($username,$password); 
        // 没查询到显示为空 var_dump($res);
        if($res){
            // 通过
            unset($res['password']);
            session("login_user", $res);
            $token = model("UserToken")->createToken($res['uid']);
            cookie('cookie_token',$token,60*60*24*90);//此处由服务端控制token是否过期，所以cookies过期时间设置多久都无所谓 —— 3 个月过期的token
            $this->sendResult(array('token' => $token)); 
        }else{
            // 缓存自增 1 待加
            $this->sendError(111111, '请输入正确用户名与密码');
        }
    }
    public function info(Request $request){
        // 获取token
        // print_r(apache_request_headers());
        // var_dump($request -> header()['connection']);exit;
        $token = array_key_exists('authorization',$request -> header()) ? $request -> header()['authorization'] : '';
        if(!$token){
            $this -> sendError(10105, '请登录');//你尚未登录$token
            exit();
        }
        // $token = $request -> header();
        // $this->sendError(111111, $request -> header());$this->sendError(111112, array_key_exists('authorization',$request -> header()));exit;
        // var_dump($this -> is_login_token());
        $uid = $this -> is_login_token($token);
        $field = "uid,username,email,name,avatar,avatar_small,groupid" ;
        $info = Db::name('user') ->where(['uid' => $uid]) -> field($field) -> find();
        $this->sendResult($info);
    }

}
