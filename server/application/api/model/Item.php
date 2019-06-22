<?php
namespace app\api\model;

use think\Model;
use think\Db;
class Item extends Model
{
    /**
     * 判断用户是否注册
     */
    
    //获取菜单结构
    public function getMemu($item_id){
        $page_field = "page_id,author_uid,cat_id,page_title,addtime";
        $catalog_field = '*';
        $data = $this->getContent($item_id , $page_field , $catalog_field) ;
        return $data ;
}

public function getContent($item_id , $page_field ="*" , $catalog_field ="*" , $uncompress = 0 ){
        //获取所有父目录id为0的页面
        $all_pages = Db::name("Page")->where(['item_id' => $item_id, 'is_del' => 0 ])->order("s_number","asc")->field($page_field)->select();
        $pages = array() ;
        if ($all_pages) {
            foreach ($all_pages as $key => $value) {
                if ($value['cat_id']) {
                    # code...
                }else{
                    $pages[] = $value ;
                }
            }
        }
        
        //获取该项目下的所有目录
        $all_catalogs = Db::name("Catalog")->field($catalog_field)->where("item_id" ,$item_id)->order("s_number","asc")->select();

        //获取所有二级目录
        $catalogs = array() ;
        if ($all_catalogs) {
            foreach ($all_catalogs as $key => $value) {
                if ($value['level'] == 2 ) {
                    $catalogs[] = $value;
                }
            }
        }
        if ($catalogs) {
            foreach ($catalogs as $key => &$catalog2) {
                //该二级目录下的所有子页面
                $catalog2['pages'] = $this->_getPageByCatId($catalog2['cat_id'],$all_pages);

                //该二级目录下的所有子目录
                $catalog2['catalogs'] =  $this->_getCatByCatId($catalog2['cat_id'],$all_catalogs);
                if($catalog2['catalogs']){
                    //获取所有三级目录的子页面
                    foreach ($catalog2['catalogs'] as $key3 => &$catalog3) {
                        //该三级目录下的所有子页面
                        $catalog3['pages'] = $this->_getPageByCatId($catalog3['cat_id'],$all_pages);

                        //该三级目录下的所有子目录
                        $catalog3['catalogs'] =  $this->_getCatByCatId($catalog3['cat_id'],$all_catalogs);
                        if($catalog3['catalogs']){
                            //获取所有三级目录的子页面
                            foreach ($catalog3['catalogs'] as $key4 => &$catalog4) {
                                //该三级目录下的所有子页面
                                $catalog4['pages'] = $this->_getPageByCatId($catalog4['cat_id'],$all_pages);
                            }                        
                        }

                    }                        
                }             
            }
        }
        $menu = array(
            "pages" =>$pages,
            "catalogs" =>$catalogs,
            );
        unset($pages);
        unset($catalogs);
        return $menu;
}

    //获取某个目录下的所有页面
    private function _getPageByCatId($cat_id ,$all_pages){
        $pages = array() ;
        if ($all_pages) {
            foreach ($all_pages as $key => $value) {
                if ($value['cat_id'] == $cat_id) {
                    $pages[] = $value ;
                }
            }
        }
        return $pages;
    }
}