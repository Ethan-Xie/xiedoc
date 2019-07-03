<?php
  namespace controller;

  use service\ApiService;
  use service\ToolService;
/**
 * 基础接口类
 */
class BasicApi
{
  // 当前请求对象
  protected $request;
  public $model = null;
  public $table = '';
  public $NotFound = 404;

  // 构造方法
  public function __construct()
  {
    ToolsService::corsOptionsHandler();
    $this->request = app('request');
//        try {
//            ApiService::write();
//        } catch (\Exception $e) {
//            $this->error($e->getMessage(), $e->getCode());;
//        }
  }

  /**
     * 返回失败的请求
     * @param mixed $msg 消息内容
     * @param array $data 返回数据
     * @param integer $code 返回代码
     */
    protected function error($msg, $code = 201, $data = [])
    {
        $code < 200 && $code += 200;
        ToolsService::error($msg, $data, $code);
    }

    /**
     * 返回资源不存在
     * @param integer $code 返回代码
     */
    protected function notAllow($code = 403)
    {
        ToolsService::error('', [], $code);
    }

    /**
     * 返回资源不存在
     * @param integer $code 返回代码
     */
    protected function notFound($code = 404)
    {
        ToolsService::error('', [], $code);
    }

}