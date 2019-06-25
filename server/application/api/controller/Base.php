<?php

namespace app\api\controller;

use think\facade\Env;
use think\Controller;
use think\Db;
use think\facade\Log;
use app\api\model;
use think\Request;
class Base extends controller{
    //是否开启本地调试
    private $is_local_debug ;
    public function __construct()
    {
        //是否开启
        $this -> is_local_debug = 1;
        //做一个检测，以免配置更新到线
        // 排除 内网地址 192.168.[0-255]+.[0-255]+
        //var_dump(Env::get('module_path'));
        if(
            $this -> is_local_debug >0
            && $_SERVER['REMOTE_ADDR'] != '127.0.0.1'
            && !preg_match("/10.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+/", $_SERVER['REMOTE_ADDR'], $matches)
            && !preg_match("/192.168.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+/",$_SERVER['REMOTE_ADDR'], $matches)
            && !preg_match("/127.(1[6-9]|2\d|3[01])+.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+.(^[0-9]\d?$|^1\d\d$|^2[0-5][0-5])+/", $_SERVER['REMOTE_ADDR'], $matches)
        ){  
            $this -> sendError('-1001','非本地环境禁止开通调试。请通知管理员关闭调试模式');
            exit();
        }else{
            /*
            // 开启跨域
            if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
                header("Access-Control-Allow-Origin: *");
                header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
                header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
                file_put_contents('option.txt',json_encode($_REQUEST));
                exit;
            }
            // 响应源
            header('Access-Control-Allow-Origin:*');
            // 响应类型
            header('Access-Control-Allow-Methods:*');
            // 响应头设置
            header('Access-Control-Allow-Headers:content-type,token,id');
            header("Access-Control-Request-Headers: Origin, X-Requested-With, content-Type, Accept, Authorization");
            */
        }
        header('Access-Control-Allow-Origin:http://localhost:8080');//允许跨域请求
        header('Access-Control-Allow-Credentials: true');//允许跨域请求
        header("Access-Control-Allow-Headers: Origin,  Accept，, Connection, User-Agent, Authorization");

        //检测数据库文件
        $this -> checkDbEhitable();
        // 为了兼容纯json 请求
        if(isset($_SERVER['CONTENT_TYPE']) && strstr($_SERVER['CONTENT_TYPE'] ,"json"))
        {
            $json = file_get_contents('php://input');
            $array = json_decode($json, 1);
            $_POST = array_merge($_POST, $array);
        }
    }

    // 验证是否登录
    public function checkLogin($redirect = true)
    {
        //debug 
        if($this -> is_local_debug >0){
            $login_user = Db::name('User') ->where("username = 'showdoc'") -> find();
            session('login_user',$login_user);
        }
        // 如果没有session（不在测试情况下）
        if( !session('login_user')){
            // 未测试
            $cookie_token = cookie('cookie_token');
            if($cookie_token){
                $ret = model("UserToken") -> getToken($cookie_token);
                if($ret && $ret['token_expire'] > time()){
                    model('UserToken') -> setLastTime($cookie_token);
                    $login_user =  Db::name('User') ->where("uid = $ret[uid]") -> find();
                    unset($ret['password']);
                    session("login_user", $login_user);
                    return $login_user;
                }
            }
            if($redirect){
                $this -> sendError(10102);//你尚未登录
                exit();
            }
        }else{
            //有session
            return session('login_user');
        }

    }

    // token 验证是否登录
    public function is_login_token($token = 0){
        if($token == 0){
            $token = array_key_exists('authorization', request()->header()) ? request()->header()['authorization'] : '';
            if($token == '' || !$token){
                $this -> sendError(10105, '请登录');//你尚未登录$token
                exit();
            }
        }
        // 检验码
        if(!model("UserToken")->decryp($token))
        {
            $this -> sendError(10106, '请登录');//你尚未登录$token
            exit();
        }

        // 获取token
        // $token = $request -> header()['Authorization'];
        //$this -> sendError(10104, $request -> header()['Authorization']);//你尚未登录
        //【debug】  $token = "1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380c1ba";
        // 查询 token
        $res = Db::name('user_token') ->where(['token' => $token]) ->find();
        if($res){
            // 是否过期
            if($res['token_expire'] > time()) 
            {
                return $res['uid'];
            }else{
                $this -> sendError(10106, '登录过期，请重新登录');//你尚未登录
                exit();
            }
        }else{
            $this -> sendError(10105, '请登录');//你尚未登录$token
            exit();
        }
    }

    // 是否有权限
    public function checkItemVisit($item_id = 0){
        $uid = $this -> is_login_token();
        if(is_numeric($uid)){
            $info = Db::name('item') ->where(['uid' => $uid,'item_id' => $item_id]) -> field('item_id') -> find();
            if($info) {
                return true;
            }else{
                $this -> sendError(10106, '对不起，您没有权限');//你尚未登录
                exit();
            }
          }else{
                $this -> sendError(10105, '请登录');//你尚未登录$token
                exit();
          }
    }

    //发送错误码 函数
    protected function sendError($error_code ,$error_message = ''){
        $error_code = $error_code ? $error_code : 111111;// 000000 未知错误码
        if(!$error_message){
            $error_codes = config('error_codes');
            foreach ($error_codes as $key => $value){
                if($key == $error_code){
                    $error_message = $value;
                }
            }
        }
        $array['error_code'] = $error_code;
		$array['error_message'] = $error_message ;
		$this->sendResult($array);
    }

    // 返回json 结果
    protected function sendResult($array)
    {
        if(isset($array['error_code']) && $array['error_code'] != 0){
            $result['error_code'] = $array['error_code'];
            $result['error_message'] =  $array['error_message'];
        }else{
            $result['error_code'] = 0;
            $result['data'] = $array;
        }

        // 如果是本地测试 ，则打开测试  
        // 未跨域名使用 $this -> is_local_debug > 0
        if( 0 ){
            /*
                class Common extends Controller
                {
                    public $param;
                    // 设置跨域访问
                    public function _initialize()
                    {
                        parent::_initialize();
                        isset($_SERVER['HTTP_ORIGIN']) ? header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']) : '';
                        header('Access-Control-Allow-Credentials: true');
                        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, authKey, sessionId,Cookie");
                        // header('Access-Control-Allow-Headers: Origin, X-Request-With, Content-Type, Accept, Connection, User-Agent, Cookie');
                        $param =  Request::instance()->param();
                        $this->param = $param;
                    }
                }

            */
            header('Access-Control-Allow-Origin:*');//允许跨域请求
            header('Access-Control-Allow-Credentials : true');//允许跨域请求
            header("Access-Control-Allow-Headers: Origin,  Accept，, Connection, User-Agent, Authorization");
            // header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
        }
        echo json_encode($result);
        //记录log
        if (Config('API_LOG')) {
			$info = '';
			$info .= "\n\n【★★★★★★★★★★★】";
			$info .= "\n请求接口：".(Env::get('module_path')) ."/".CONTROLLER_NAME."/".ACTION_NAME."";
			$info .= "\n请求".'$_REQUEST'."：\n";
			$info .= json_encode($_REQUEST);
			$info .= "\n返回结果：\n";
			$info .= json_encode($result)."\n";	
			$info .= "【★★★★★★★★★★★】\n";		
			\Think\log::record($info , 'INFO');
		}
    }

    protected function checkDbEhitable()
    {
        $str = config("database.dsn");
        if(empty($str)){
            $this -> sendError('-10003',"Sqlite/showdoc.db.php路径配置不正确");
	    	exit();
        }
        $file = explode(":",$str);
        $file = $file[1];
        if( $fp = fopen($file, 'a+'))
        {
            @fclose($fp);
            return true;
        }else{
            $this -> sendError('10103',"Sqlite/showdoc.db.php文件不可写");
	    	exit();
        }
    }

    //判断某用户是否有项目管理权限（项目成员member_group_id为1，是项目所在团队的成员并且成员权限为1 ，以及 项目创建者）
	protected function checkItemPermn($uid , $item_id){

		if (!$uid) {
			return false;
		}

		if (session("mamage_item_".$item_id)) {
			return true;
		}

		$item = Db::name("Item")->where("item_id",$item_id)->find();
		if ($item['uid'] && $item['uid'] == $uid) {
			session("mamage_item_".$item_id , 1 );
			return true;
		}
		$ItemMember = D("ItemMember")->where(['item_id' =>$item_id,'uid' =>$uid,'member_group_id' => 1 ] )->find();
		if ($ItemMember) {
			session("mamage_item_".$item_id , 1 );
			return true;
		}

		$ItemMember = D("TeamItemMember")->where(['item_id' =>$item_id,'member_uid' =>$uid,'member_group_id' => 1 ])->find();
		if ($ItemMember) {
			session("mamage_item_".$item_id , 1 );
			return true;
		}

		return false;
    }
    
    //判断某用户是否为项目创建者
	protected function checkItemCreator($uid , $item_id){
		if (!$uid) {
			return false;
		}
		if (session("creat_item_".$item_id)) {
			return true;
		}

		$item = Db::name("Item")->where("item_id",$item_id)->find();
		if ($item['uid'] && $item['uid'] == $uid) {
			session("creat_item_".$item_id , 1 );
			return true;
		}
		return false;
	}
}
