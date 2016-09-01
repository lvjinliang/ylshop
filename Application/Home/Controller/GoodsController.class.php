<?php
namespace Home\Controller;

use Think\Controller;

class GoodsController extends CommonController {
    private $Goods = '';
    private $Category = '';

    public function _initialize() {
        parent::_initialize();
        $this->Goods = D('Goods');
        $this->Category = D('Category');
    }

    public function index() {
        $id = I('get.id');
        if (empty($id)) {
            $this->_empty();
            exit();
        }
        $data = array();
        $data['goodsInfo'] = $this->Goods->getGoodsById($id);
        if (empty($data['goodsInfo'])) {
            $this->_empty('该商品不存在或已下架');
            exit();
        }
        $scripts = array(array('src' => __ROOT__ . '/Public/Home/js/swiper.min.js'));
        $style = array(array('href' => __ROOT__ . '/Public/Home/css/swiper.min.css'));
        $this->setScript($scripts);
        $this->setStyle($style);
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $path = $this->Category->getCategoryPathById($data['goodsInfo']['category_id']);
        foreach ($path as $key => $val) {
            $breadcrumbs[] = array('title' => $val['name'], 'href' => U('Home/category/index', array('id' => $val['id'])));
        }
        $breadcrumbs[] = array('title' => $data['goodsInfo'] ['name'], 'href' => 'javascript:void(0);');
        $data['leftCategory'] = $this->Category->getChildrenById($path[0]['id']);
        //获取商品评价
        $data['review'] = $this->goods_review($id);
        setLastView($id);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->assign('changeGoodsAttrUrl', U('Home/goods/change_goods_attr'));
        $this->display();
    }

    private function goods_review() {
        $id = I('get.id');
        $data = array();
        $filter = array();
        $Review = D('Review');
        $count = $Review->getTotalReviewByGoodsId($id,$filter);
        $page = setPage($count, C('PAGE_SIZE'), 'Home/goods/ajax_goods_review');
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $filter['order'] = 'r.date_added DESC, r.id DESC';
        $data['reviewList'] = $Review->getReviewByGoodsId($id,$filter);
        $this->assign('data', $data);
        return $this->fetch('Goods/goods_review');
    }
    public function ajax_goods_review() {
        echo $this->goods_review();
        exit();
    }

    public function change_goods_attr() {
        layout(false);
        $id = I('post.id');
        $attrIds = I('post.attr_ids');
        $attrIds = explode(',', $attrIds);
        $attrValues = I('post.attr_values');
        $attrValues = explode(',', $attrValues);
        $json = array('success' => '0', 'data' => '');
        if (!empty($id) && !empty($attrIds) && !empty($attrValues)) {
            $products = $this->Goods->getProductByGoodsId($id);
            $goodsAttr = $this->Goods->getGoodsAttrByGoodsId($id);
            $attrPrice = 0;
            $productSn = '';
            $productNumber = 0;
            foreach ($goodsAttr as $val) {
                if (in_array($val['attr_id'], $attrIds) && in_array($val['attr_value'], $attrValues)) {
                    $attrPrice = $attrPrice + $val['attr_price'];
                }
            }
            foreach ($products as $key => $val) {
                $keys = explode(',', $key);
                if (count(array_intersect($keys, $attrValues)) == count($keys)) {
                    $attrIdsKey = explode(',', $val['attr_ids']);
                    if (count(array_intersect($attrIdsKey, $attrIds)) == count($attrIdsKey)) {
                        $productSn = $val['product_sn'];
                        $productNumber = $val['product_number'];
                    }
                }

            }
            $json['success'] = 1;
            $json['data'] = array(
                'attrPrice' => $attrPrice,
                'productSn' => $productSn,
                'productNumber' => $productNumber,
            );
        }

        $this->ajaxReturn($json);
    }
}