<?php
namespace app\api\model;

use think\Model;

class User extends Model
{
    /**
     * 判断用户是否注册
     */
    public function isExist($username){
        return $this -> where(['username'=> $username]) -> find();
    }

    public function register($username, $password)
    {
        $password = md5(base64_encode(md5($password).'xie0610THAN'));
        $list = [
            'username' => $username,
            'password' => $password,
            'reg_time' => time()
        ];
        return $this-> save($list);
    }

    // 修改密码 
    public function updatePwd($uid, $password)
    {
        $password = md5(base64_encode(md5($password).'xie0610THAN'));
        return $this -> save(['password' => $password], ['uid' => $uid]);
    }
    /**
     * 用户名，密码的验证
     */
    public function checkLogin($username, $password)
    {
        $password = md5(base64_encode(md5($password).'xie0610THAN'));
        return $this -> where(['username'=> $username, 'password' => $password]) -> find();
    }

    //检测ldap登录 （没用到）
    public function checkLdapLogin($username ,$password ){
        $ldap_open = D("Options")->get("ldap_open" ) ;
        $ldap_form = D("Options")->get("ldap_form" ) ;
        $ldap_form = json_decode($ldap_form,1);
        if (!$ldap_open) {
            return false;
        }
        $ldap_conn = ldap_connect($ldap_form['host'], $ldap_form['port']);//建立与 LDAP 服务器的连接
        if (!$ldap_conn) {
           return false;
        }
        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_form['version']);
        $rs=ldap_bind($ldap_conn, $ldap_form['bind_dn'], $ldap_form['bind_password']);//与服务器绑定 用户登录验证 成功返回1 
        if (!$rs) {
           return false ;
        }

        $result = ldap_search($ldap_conn,$ldap_form['base_dn'],"(cn=*)");
        $data = ldap_get_entries($ldap_conn, $result);
        for ($i=0; $i<$data["count"]; $i++) {
            $ldap_user = $data[$i]["cn"][0] ;
            $dn = $data[$i]["dn"] ;
            if ($ldap_user == $username) {
                //如果该用户不在数据库里，则帮助其注册
                $userInfo = D("User")->isExist($username) ;
                if(!$userInfo){
                    D("User")->register($ldap_user,$ldap_user.time());
                }
                $rs2=ldap_bind($ldap_conn, $dn , $password);
                if ($rs2) {
                   D("User")->updatePwd($userInfo['uid'], $password);
                   return $this->checkLogin($username,$password);
                }
            }
        }

        return false ;

}

    // 获取信息
    
}