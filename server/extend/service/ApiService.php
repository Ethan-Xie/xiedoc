<?php
  namespace service;
  use think\Db;
  use think\Query;
  use think\Request;

  /**
   * 接口服务
   * class ApiService
   */

  class ApiService
  {
    /**
     * 获取接口操作对象
     * 
     */
    protected static function db()
    {
      return Db::name('ApiLog');
    }
    // api 调用日志
    public static function write($from = '', $content = '') {
      //todo 使用缓存

      $ip = Request::ip();
      $module = Request::module();
      $node = strtolower(join('/', [$module, Request::controller(), Request::action()]));
      self::checkTraffic($node, $ip);
      $header = Request::header();
      switch ($module) {
          case 'project':
              $session = SessionService::getMemberSession();
              $action = 'PMS';
              $actionName = $session['name'];
              $actionId = $session['id'];
              break;
          case 'index':
              $action = 'Common';
              $actionName = '公共接口';
              $actionId = 0;
              break;
          default:
              $session = SessionService::getAdminSession();
              $action = 'Admin';
              $actionName = $session['username'];
              $actionId = $session['id'];
      }
      $nodeName = Db::name('SystemNode')->where(['node' => $node])->field("title")->find();
      if ($nodeName) {
          $nodeName = $nodeName['title'];
      }
      $data = [
          'ip' => $ip,
          'node' => $node,
          'node_name' => $nodeName,
          'action' => $action,
          'action_name' => $actionName,
          'action_id' => $actionId,
          'content' => $content,
          'module' => $module,
          'from' => $from,
          'user_agent' => $header['user-agent'],
          'seconds' => time(),
      ];
      return self::db()->insert($data) !== false;
    }
  }