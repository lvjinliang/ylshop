<?php
namespace Admin\Event;
use Think\Controller;

class GoodsEvent extends Controller {

    public $Goods = '';

    public function _initialize() {
        $this->Goods = D('Goods');
    }

    public function delete($id) {
        if($this->Goods->where(array('id'=>array('in',$id)))->delete()!==false){
            //删除分类
            $GoodsCategory = D('GoodsCategory');
            $GoodsCategory->where(array('goods_id'=>array('in',$id)))->delete();
            //删除属性
            $GoodsAttr = D('GoodsAttr');
            $GoodsAttr->where(array('goods_id'=>array('in',$id)))->delete();
            //删除推荐位
            $PositionGoods = D('PositionGoods');
            $PositionGoods->where(array('goods_id'=>array('in',$id)))->delete();
            //删除相册
            $GoodsGallery = D('GoodsGallery');
            $GoodsGallery->where(array('goods_id'=>array('in',$id)))->delete();
            //删除搜索词
            M('Search')->where(array('goods_id'=>array('in', $id)))->delete();
            return true;
        } else {
            return false;
        }
    }


}