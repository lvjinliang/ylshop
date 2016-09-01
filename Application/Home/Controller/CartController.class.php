<?php
namespace Home\Controller;
use Think\Controller;

class CartController extends CommonController {
    private $Cart = '';
    private $error = array();

    public function _initialize() {
        parent::_initialize();
        $this->Cart = new \Org\Home\Cart($this->accountObj);
    }

    public function index() {
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '购物车', 'href' => 'javascript:void(0)');
        $data = array();
        $data['cartList'] = $this->cart_list();
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('showMenu', 0);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * @param bool $hasSelected
     * @return mixed
     */
    private function cart_list($hasSelected=false) {
        $selectGoods = I('post.selectGoods');
        $EventCart = A('Cart', 'Event');
        $data = $EventCart->cart_list($this->Cart,'cart',$selectGoods, $hasSelected);
        $this->assign('data', $data);
        return $this->fetch('Cart/cart_list');
    }



    public function add_cart() {
        layout(false);
        $json = array('success'=>1,'msg'=>'');
        $goods = I('post.goods');
        if (empty($goods)) {
            $json = array('success'=>0,'msg'=>'没有选择商品');
            $this->ajaxReturn($json);
            exit();
        } else {
            $this->checkCartData($goods);
        }
        $result = $this->Cart->addCart($goods);
        if($result) {
            $json = array('success'=>1,'total'=>$result);
        }
        $this->ajaxReturn($json);
        exit();

    }

    private function checkCartData($goods) {
        $Goods = D('Goods');
        foreach ($goods as $v) {
            if (empty($v['goods_id'])) {
                $json = array('success'=>0,'msg'=>'请选择商品');
                $this->ajaxReturn($json);
                exit();
            }
            if (empty($v['number'])) {
                $json = array('success'=>0,'msg'=>'请填写数量');
                $this->ajaxReturn($json);
                exit();
            }

            if(!preg_match('/^[1-9][0-9]?$/i',$v['number'])) {
                $json = array('success'=>0,'msg'=>'请写入正确的数量');
                $this->ajaxReturn($json);
                exit();
            }

            $addResult = $Goods->checkAddToCart($v['goods_id'], $v['number'], $v['product_sn']);
            //{1:商品不存,2:商品库存不足,3:正确}
            switch ($addResult) {
                case 1:
                    $json = array('success'=>0,'msg'=>'商品不存');
                    $this->ajaxReturn($json);
                    exit();
                    break;
                case 2:
                    $json = array('success'=>0,'msg'=>'商品库存不足');
                    $this->ajaxReturn($json);
                    exit();
                    break;
                case 3:
                    break;
                default:
                    $json = array('success'=>0,'msg'=>'数据非法');
                    $this->ajaxReturn($json);
                    exit();

            }
            if(!$Goods->checkAddToCart($v['goods_id'], $v['number'], $v['product_sn'])) {
                $json = array('success'=>0,'msg'=>'商品库存不足');
                $this->ajaxReturn($json);
                exit();
            }

        }
    }

    public function remove_cart() {
        layout(false);
        $goods = I('post.goods');
        $this->Cart->removeCart($goods);
        echo $this->cart_list(true);
        exit();
    }

    public function update_cart() {
        layout(false);
        $json = array('success'=>1,'msg'=>'');
        $goods = I('post.goods');
        if (empty($goods)) {
            $json = array('success'=>0,'msg'=>'没有选择商品');
            $this->ajaxReturn($json);
            exit();
        } else {
            $this->checkCartData($goods);
        }
        $result = $this->Cart->updateCart($goods);
        if($result) {
            $json = array('success'=>1,'html'=>$this->cart_list(true));
        }
        $this->ajaxReturn($json);
        exit();
    }

    public function select_cart() {
        layout(false);
        echo $this->cart_list(true);
        exit();
    }


}