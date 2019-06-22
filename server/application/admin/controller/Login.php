<?php
namespace app\admin\controller;
use think\Controller;
class Login extends Controller {
    
	//用户的登陆 
	public function login(){
     
	  //判断用户是否登陆成功

	   if(isset($_POST['username']) && isset($_POST['password'])){
	   	//echo "我接收到数据";
		$admin = M('users');
		$map = $_POST;
		unset($map['sub']);
		$map['usertype'] = '2';
		$data = $admin -> where($map) -> find();
		
		//通过验证 
		if($data){
			
		 $_SESSION['admin'] = $data;
		// dump($data);
		 $_SESSION['islogin'] = 1;
		 $this -> redirect('Index/index');
		
		}else{
			$this ->error("用户名或密码不正确");
		}

	}
		return $this->fetch();  
}

	// // 退出登录
	public function loginOut(){
		unset($_SESSION['admin']);
		unset($_SESSION['islogin']);
		$this -> redirect('Login/login');
	// }
  }	
	
}