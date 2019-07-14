<?php

namespace app\common\Model;

use app\shop\Model\ShopGoods;
use service\FileService;
use service\ToolsService;
use think\facade\Request;
use think\File;
use think\Model;

class CommonModel extends Model
{
  protected $connection = [
        //'type'            => 'mysql',
        'type'          => 'mysql',
        // 服务器地址
        'hostname'        => '127.0.0.1',//'127.0.0.1',
        // 数据库名
        'database'        => 'xie_doc',//
        // 用户名
        'username'        => 'root',//'showdoc',
        // 密码
        'password'        => '',//'showdoc123456',
        // 端口
        'hostport'        => '3306',//'3306',
        // 连接dsn
        'dsn'             => '',//sqlite 配置 sqlite:../Sqlite/showdoc.db.php
        // 数据库连接参数
        'params'          => [],
        // 数据库编码默认采用utf8
        'charset'         => 'utf8',
        // 数据库表前缀
        'prefix'          => 'pear_',
    ];

    /**
     * 返回失败的请求
     * @param mixed $msg 消息内容
     * @param array $data 返回数据
     * @param integer $code 返回代码
     */
    protected function error($msg, $data = [], $code = 400)
    {
        ToolsService::error($msg, $data, $code);
    }

    /**
     * 分页方法
     * @param null $where 可以传入查询对象或模型实例
     * @param $order
     * @param $field
     * @param bool $simple 是否简介模式，简介模式不分页
     * @param array $config 分页配置，page: 当前页，rows: 每页数量
     * @return array
     * @throws \think\exception\DbException
     */
    public function _list($where = null, $order = 'id desc', $field = null, $simple = false, $config = [])
    {
        $rows = intval(Request::param('pageSize', cookie('pageSize')));
        if (!$rows) {
            $rows = 10;
        }
        cookie('pageSize', $rows);
        $config['query'] = Request::param();
        $whereOr = [];
        if (isset($where['or']) and $where['or']) {
            //todo 怎么or连贯查询
            /*
             * whereOr查询，形式如：
            $where['or'][]= ['name','like',"xxx"];
            $where['or'][] = ['id','=',"xxx"];
            */
            $whereOr = $where['or'];
            unset($where['or']);
        }
        $page = $this->where($where)->whereOr($whereOr)->order($order)->field($field)->paginate($rows, $simple, $config);
        $list = $page->all();
        $result = ['total' => $simple ? count($list) : $page->total(), 'page' => $page->currentPage(), 'list' => $list];
        return $result;
    }

    public function _listWithTrashed($where = null, $order = null, $field = null, $simple = false, $config = [])
    {
        $rows = intval(Request::param('rows', cookie('pageSize')));
        if (!$rows) {
            $rows = 10;
        }
        cookie('pageSize', $rows);
        $config['query'] = Request::param();
        $whereOr = [];
        if (isset($where['or']) and $where['or']) {
            //todo 怎么or连贯查询
            /*
             * whereOr查询，形式如：
            $where['or'][]= ['name','like',"xxx"];
            $where['or'][] = ['id','=',"xxx"];
            */
            $whereOr = $where['or'];
            unset($where['or']);
        }
        $class = get_class($this);
        $count = $class::withTrashed()->where($where)->whereOr($whereOr)->order($order)->field($field)->count();
        $page = $config['query']['page'] ? $config['query']['page'] : 1;
        $offset = $rows * ($config['query']['page'] - 1);
        $list = $class::withTrashed()->where($where)->whereOr($whereOr)->order($order)->field($field)->limit($offset, $rows)->select();
        $result = ['total' => $count, 'page' => $page, 'list' => $list];
        return $result;
    }

    public function _edit($data, $where = [])
    {
        return $this->isUpdate(true)->save($data,$where);
    }

    public function _add($data)
    {
        $obj = $this::create($data);
        if ($obj->id) {
            return $this::get($obj->id);
        }
        return false;
    }

    /**
     * @param File $file
     * @param $path_name
     * @return array|bool
     * @throws \OSS\Core\OssException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @throws \Exception
     */
    public function _uploadImg(File $file, $path_name = '')
    {
        if (!$path_name) {
            $path_name = config('upload.base_path') . config('default');
        }
        if (!$file->checkExt(strtolower(sysconf('storage_local_exts')))) {
            \exception('文件上传类型受限', 1);
        }
        $path = $path_name;
        $info = $file->move($path);
        if ($info) {
            $filename = str_replace('\\', '/', $path . '/' . $info->getSaveName());
//            $image = \think\Image::open($info->getRealPath());
//            $image->thumb($image->width() / 2, $image->height() / 2)->save($filename);//压缩
            $site_url = FileService::getFileUrl($filename, 'local');
            $fileInfo = FileService::save($filename, file_get_contents($site_url));
            if ($fileInfo) {
                return ['base_url' => $fileInfo['key'], 'url' => $fileInfo['url'], 'filename' => $file->getInfo('name')];
            }
        }
        return false;
    }

    /*
     * 获取当前organization id
     * */
    public function gecurrentOrganizationCode(){
        $currentOrganizationCode = session('currentOrganizationCode');
        return $currentOrganizationCode;
    }

    /*
    * 获取当前member session
    * */
    public function getMemberSession(){
        $member_session = session('member');
        return $member_session;
    }

}
