<?php
namespace app\admin\controller;
use think\Controller;
class Index extends Controller {
    
	//用户的登陆 
	public function index(){
        
        return $this -> fetch();
    }

    public function test(){
        
        return $this -> fetch();
    }
    public function test2(){
        
        return $this -> fetch();
    }

    }