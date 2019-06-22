<?php
namespace app\api\controller;


use think\Db;
use think\facade\Log;
class Index extends Base
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello()
    {
        //return 'hello,' . $name;
        $list= Db::table("User")->where("username = 'showdoc' ")->select();
        var_dump($list);
        Log::info('日志信息');
        $this -> sendError(10101);
        return;
    }
    public function test()
    {
        session_start();
        $data=['hello' => '1', 'hello2' => '1'];
        //session('data',$data);
        // var_dump(session('data'));
        // return ;
        $_SESSION['hello1']=$data;
        var_dump($_SESSION);
    }

    public function encryp(){
        $token = '1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380c1ba';
        // $token = '1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380';
        $flag1 = substr($token , 0, 60);
        $flag2 =$flag1.substr(md5( $flag1 ), 0, 4);// bd2fb3c614cb77a8bdc0ff04d4cd802b ->  bd2f
        var_dump($flag1, $flag2);

        $flag1 = substr($token , 0, 60);
        return $flag1.substr(md5( $flag1 ), 0, 4);

    }
    public function decryp(){
        // 1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380bd2f
        $token = '1333906bec4341a9f22c6c48e4f73e51ac58ae5cd4928a9a16781e31d380bd2f';
        $flag1 = substr($token , 0, 60);
        $flag2 =substr(md5( $flag1 ), 0, 4);
        var_dump($flag2, substr($token , 60));

        return (substr(md5( substr($token , 0, 60) ), 0, 4) == substr($token , 60));
    }

    public function res(){
        $this -> sendResult($_POST);
    }
    
}
