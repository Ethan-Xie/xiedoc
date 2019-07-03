<?php
namespace app\api\controller;


use think\Db;
use think\facade\Log;
use think\Request;
class Item extends Base
{
    public $uid = 0;
    public function __construct(){
      parent::__construct();
      // author 认证 没通过会直接退出
      // $this->uid = $this -> is_login_token();

    }
  // 项目列表  待加
    public function myList()
    {
        // return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">12载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
        //.page_content
        // that.page_title = response.data.data.page_title
        $info = array('test');
        // $this->sendResult($this->uid);exit;
        // $info['page_title'] = '### 测试title';
        // $info['page_content'] = '### 测试';
        //[{item_id: "164320761100421", item_name: "API文档示例", item_domain: "", item_type: "1",item_description: "API文档示例"

        // $info[0] = ['is_del'=> "0",'item_id'=> "164320761100421", 'item_name'=> "API文档示例", 'item_domain'=> "", 'item_type'=> "1",'item_description'=> "API文档示例",
        // 'item_type'=> "1",'last_update_time'=>"1546500172",'top'=> 1];

        // $login_user['uid'] = $this->uid();  

        $member_item_ids = array(-1) ; 
        $item_members = Db::name("ItemMember")->where(['uid' => $this->uid])->select();
        if ($item_members) {
            foreach ($item_members as $key => $value) {
                $member_item_ids[] = $value['item_id'] ;
            }
        }
        $team_item_members = Db::name("TeamItemMember")->where(['member_uid' => $this->uid])->select();
        if ($team_item_members) {
            foreach ($team_item_members as $key => $value) {
                $member_item_ids[] = $value['item_id'] ;
            }
        }
        
        $items  = Db::name("Item")->field("item_id,item_name,item_domain,item_type,last_update_time,item_description,is_del")
        ->whereor([['uid','=',$this->uid], ['item_id','in', $member_item_ids]])->order("item_id asc")->select();
        
        
        foreach ($items as $key => $value) {
            //如果项目已标识为删除
            if ($value['is_del'] == 1) {
                unset($items[$key]);
            }
        }
        $items = array_values($items);
        //读取需要置顶的项目
        $top_items = Db::name("ItemTop")->where(['uid' => $this->uid])->select();
        if ($top_items) {
            $top_item_ids = array() ;
            foreach ($top_items as $key => $value) {
                $top_item_ids[] = $value['item_id'];
            }
            foreach ($items as $key => $value) {
                $items[$key]['top'] = 0 ;
                if (in_array($value['item_id'], $top_item_ids) ) {
                    $items[$key]['top'] = 1 ;
                    $tmp = $items[$key] ;
                    unset($items[$key]);
                    array_unshift($items,$tmp) ;
                }
            }
        }

        $items = $items ? array_values($items) : array();
        $this->sendResult($items);

       
    }

    // 项目接口 /api/item/info
    public function info(Request $request){
      // 单个项目信息
      /*
      default_page_id: ""
      item_id: "164320761100421"
      keyword: ""
      */
      $item_id = trim($request->param('item_id'));
      $item_domain = trim($request->param('item_domain'));
      $page_id = trim($request->param('page_id'));

      // 如果不是数字 ，直接赋值给单独域名
      if( ! is_numeric($item_id)){
        $item_domain = $item_id;
      }

      // 判断个性域名
      if ($item_domain){
        $info = Db::name('item') ->where(['item_domain' => $item_domain]) -> field('item_id') -> find();
        if ($info['item_id']) {
          $item_id = $info['item_id'];
        }else {
          return ;
        }
      }

      // 判断是否有可读权限
      if(! $this->checkItemVisit($item_id)){
        $this -> sendError(10106, '对不起，您没有权限');
      }
      $item = Db::name("item") ->where(['item_id' => $item_id]) -> find();
      if (!$item || $item['is_del'] == 1) {
        sleep(1);
        $this->sendError(10101,'项目不存在或者已删除');
        return false;
    }
    if ($item['item_type'] == 1 ) {
        $this->_show_regular_item($item);
    }
    elseif ($item['item_type'] == 2 ) {
        $this->_show_single_page_item($item);
    }else{
       $this->_show_regular_item($item); 
    }

      // $this->sendResult($_POST);
    }

    // 新建项目
    public function add(){
      if( is_numeric($this->uid)){
        $login_user = $this->uid;
      }else{
        $this -> sendError(111111, '未知错误');
      }
      $item_name = request()->post("item_name");
      $item_domain = request()->post("item_domain") ? request()->post("item_domain") : '';
      $copy_item_id = request()->post("copy_item_id");
      $password = request()->post("password");
      $item_description = request()->post("item_description");
      $item_type = request()->post("item_type");

      // 个性域名
      if ($item_domain) 
      {
        if(!ctype_alnum($item_domain) ||  is_numeric($item_domain) ){
            //echo '个性域名只能是字母或数字的组合';exit;
            $this->sendError(10305);
            return false;
        }
        $item = Db::name("Item")->where(["item_domain" => $item_domain])->find();
        if ($item) {
            //个性域名已经存在
            $this->sendError(10304);
            return false;
        }
    }
    //如果是复制项目
    if ($copy_item_id > 0) {
      if (!$this->checkItemPermn($login_user['uid'] , $copy_item_id)) {
          $this->sendError(10103);
          return;
      }
      $ret =  Db::name("Item")->copy($copy_item_id,$login_user['uid'],$item_name,$item_description,$password,$item_domain);
      if ($ret) {
          $this->sendResult(array());             
      }else{
          $this->sendError(10101);
      }
      return ;
  }
  
  $insert = array(
      "uid" => $login_user['uid'] ,
      "username" => $login_user['username'] ,
      "item_name" => $item_name ,
      "password" => $password ,
      "item_description" => $item_description ,
      "item_domain" => $item_domain ,
      "item_type" => $item_type ,
      "addtime" =>time()
      );
        $item_id = Db::name("Item")->insert($insert);

        if ($item_id) {
          //如果是单页应用，则新建一个默认页
          if ($item_type == 2 ) {
              $insert = array(
                  'author_uid' => $login_user['uid'] ,
                  'author_username' => $login_user['username'],
                  "page_title" => $item_name ,
                  "item_id" => $item_id ,
                  "cat_id" => 0 ,
                  "page_content" => '欢迎使用showdoc。点击右上方的编辑按钮进行编辑吧！' ,
                  "addtime" =>time()
                  );
              $page_id =  Db::name("Page")->insert($insert);
          }
          $this->sendResult(array());               
      }else{
          $this->sendError(10101);
      }
    }

    //展示常规项目
    private function _show_regular_item($item){
      $item_id = $item['item_id'];

      $default_page_id = request()->post("default_page_id"); // ? request()->post("default_page_id") : '';
      $keyword = request()->post("keyword");
      $default_cat_id2 = $default_cat_id3 = 0 ;

      // $login_user = session("login_user");
      // $uid = $login_user['uid'] ? $login_user['uid'] : 0 ;.
      $uid = $this ->uid;
      $is_login =   $uid > 0 ? true :false;
      $menu = array(
          "pages" =>array(),
          "catalogs" =>array(),
          );
      //是否有搜索词
      if ($keyword) {
          $keyword = \SQLite3::escapeString($keyword) ;
          $pages = Db::name("Page")->where("item_id = '$item_id' and is_del = 0  and ( page_title like '%{$keyword}%' or page_content like '%{$keyword}%' ) ")->order(" `s_number` asc  ")->field("page_id,author_uid,cat_id,page_title,addtime")->select();
          $menu['pages'] = $pages ? $pages : array();
      }else{
          $menu = model("Item")->getMemu($item_id) ;
      }

      $domain = $item['item_domain'] ? $item['item_domain'] : $item['item_id'];
      $share_url = get_domain().'/'.$domain;

      $ItemPermn = $this->checkItemPermn($uid , $item_id) ;

      $ItemCreator = $this->checkItemCreator($uid , $item_id);

      //如果带了默认展开的页面id，则获取该页面所在的二级目录/三级目录/四级目录
      if (1) {
          $page = Db::name("Page")->where(' page_id',$default_page_id)->find();
          if ($page) {
              $default_cat_id4 = $page['cat_id'] ;
              $cat1 = Db::name("Catalog")->where('cat_id' ,$default_cat_id4)-> where('parent_cat_id','>',0)->find();
              if ($cat1) {
                  $default_cat_id3 = $cat1['parent_cat_id'];
              }else{
                  $default_cat_id3 = $default_cat_id4;
                  $default_cat_id4 = 0 ;
              }

              $cat2 = Db::name("Catalog")->where('cat_id' ,$default_cat_id3)-> where('parent_cat_id','>',0)->find();
              if ($cat2) {
                  $default_cat_id2 = $cat2['parent_cat_id'];
              }else{
                  $default_cat_id2 = $default_cat_id3;
                  $default_cat_id3 = 0 ;
              }
          }else{
            $default_cat_id4 = 0 ;
          }
      }

      // if (LANG_SET == 'en-us') {
      //     $help_url = "https://www.showdoc.cc/help-en";
      // }
      // else{
      //     $help_url = "https://www.showdoc.cc/help";
      // }

        // $unread_count  
      $return = array(
          "item_id"=>$item_id ,
          "item_domain"=>$item['item_domain'] ,
          "is_archived"=>$item['is_archived'] ,
          "item_name"=>$item['item_name'] ,
          "default_page_id"=>(string)$default_page_id ,
          "default_cat_id2"=>$default_cat_id2 ,
          "default_cat_id3"=>$default_cat_id3 ,
          "default_cat_id4"=>$default_cat_id4 ,
          "unread_count"=>0,
          "item_type"=>1 ,
          "menu"=>$menu ,
          "is_login"=>$is_login,
          "ItemPermn"=>$ItemPermn ,
          "ItemCreator"=>$ItemCreator ,

          );
      $this->sendResult($return);
  }

  public function test()
  {
    Session_start();
    $this->sendResult(['name' => session_name(), 'id' => session_id()]);
  }

}

