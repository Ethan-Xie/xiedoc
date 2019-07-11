<?php
namespace app\common\Model;
use think\Db;
use think\File;

class Member extends CommonModel {
  
  protected $append = [];

  public function login($account) {
    if ($account == 'admin') {
      return [];
    }
    $where[] = ['account', '=', $account];
    return Db::name('member') -> where($where) -> find();
  }

  // 上传 img
  public function uploadImg(File $file)
  {
    return $this -> _uploadImg($file, config('upload.base_path'),. config('upload.member_avatar'));
  }

}