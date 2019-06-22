<?php
namespace app\api\model;

use think\Model;

class Options extends Model
{
    public function get_value($option_name)
    {
        $res = $this ->  where("option_name",$option_name) ->find() ;
        if($res){
            return $res['option_value'];
        }
        return false;
    }

    public function set($option_name, $option_value){
        $arr = [
            'option_name' => $option_name,
            'option_value'=> $option_value
        ];
        return $this -> save($arr); 
    }
}