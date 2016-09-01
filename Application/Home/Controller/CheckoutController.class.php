<?php
namespace Home\Controller;

use Think\Controller;

class CheckoutController extends CommonController {
    private $Cart = '';
    private $cartInfo = '';
    private $error = array();

    public function _initialize() {
        parent::_initialize();
        $this->Cart = new \Org\Home\Cart($this->accountObj);
    }

    public function index() {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '购物车', 'href' => U('Home/cart/index'));
        $breadcrumbs[] = array('title' => '结单', 'href' => 'javascript:void(0)');
        $data = array();
        $data['checkout-content'] = $this->checkout_content();
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('showMenu', 0);
        $this->assign('data', $data);
        $this->display();
    }

    private function checkout_content() {
        if (!session('?checkout.addressId')) {
            session('checkout.addressId', $this->accountObj->accountInfo['address_id']);
        }
        $data = array();

        //积分
        $payIntegral = I('post.pay_integral');
        $payIntegral = (int)$payIntegral;
        $data['use_pay_integral'] = $payIntegral;
        //余额
        $money = I('post.money');
        $money = (int)$money;
        $data['use_money'] = $money;
        //优惠券
        $data['coupon'] = I('post.coupon');
        $Shipping = D('Shipping');
        $Payment = D('Payment');

        //购物车信息
        $EventCart = A('Cart', 'Event');
        $this->cartInfo = $EventCart->cart_list($this->Cart, 'checkout');
        $data['goods'] = $this->cartInfo['selectGoods'];
        $data['totalRow'] = $this->cartInfo['totalRow'];
        $data['totals'] = $this->cartInfo['totals'];
        $data['pay_integral'] = $this->accountObj->getIntergral();
        $data['money'] = $this->accountObj->accountInfo['money'];
        $data['canUseIntegral'] = $this->cartInfo['canUseIntegral'];
        $data['gift'] = $this->cartInfo['gift'];
        $data['usePromotions'] = $this->cartInfo['usePromotions'];
        $data['couponList'] = $this->cartInfo['couponList'];
        $this->error = $this->cartInfo['error']; //获取错误信息

        $data['addressList'] = $this->get_addressList();
        $data['shippingList'] = $Shipping->getList();
        $data['paymentList'] = $Payment->getList();
        //发票
        $data['invoice_status'] = I('post.invoice_status');
        $data['invoice_status'] = empty($data['invoice_status']) ? 0 : 1;
        $data['invoice_title'] = I('post.invoice_title');
        $data['invoice_type'] = I('post.invoice_type');
        $data['comment'] = I('post.comment');
        $data['error'] = $this->error;
        $this->assign('data', $data);
        return $this->fetch('Checkout/checkout_content');
    }

    public function ajax_get_content() {
        $json = array('success' => 1, 'html' => '', 'msg' => '');
        if (!$this->accountObj->isLogin()) {
            $json['html'] = urlRedirect(U('Home/login/index'));
            $this->ajaxReturn($json);
            exit();
        }

        $json['html'] = $this->checkout_content();
        if (!empty($this->error)) {
            $json['success'] = 0;
        }
        $this->ajaxReturn($json);
        exit();
    }


    public function ajax_get_addressList() {
        layout(false);
        echo $this->get_addressList();
        exit();
    }

    public function order() {
        $json = array('success' => 0, 'html' => '', 'msg' => '');
        if (!$this->accountObj->isLogin()) {
            $json['html'] = urlRedirect(U('Home/login/index'));
            $this->ajaxReturn($json);
            exit();
        }
        $json['html'] = $this->checkout_content();
        if (!empty($this->error)) {
            $json['success'] = 0;
        } else {
            $Order = D('Order');
            $orderId = $Order->addOrder(I('post.'),$this->cartInfo);
            if (!$orderId) {
                $json['success'] = 0;
                $json['msg'] = '服务器异常';
            } else {
                $orderNo = $Order->getOrderNoByOrderId($orderId);
                $json['success'] = 1;
                $json['jumpUrl'] = U('home/checkout/success',array('id'=>$orderNo));
                $removeCart = array();
                foreach($this->cartInfo['selectGoods'] as $key=>$val) {
                    $removeCart[$key]['goods_id'] = $val['id'];
                    $removeCart[$key]['product_sn'] = $val['product_sn'];
                }
                $this->Cart->removeCart($removeCart);
                session('checkout',null);
            }
        }
        $this->ajaxReturn($json);
        exit();
    }

    private function get_addressList() {
        $AccountAddress = D('AccountAddress');
        $data = $AccountAddress->getAddressListByAccountId($this->accountObj->getAccountId());
        $this->assign('data', $data);
        return $this->fetch('Checkout/addressList');
    }

    public function success() {
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '支付', 'href' => 'javascript:void(0)');
        $this->assign('showMenu', 0);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('content', $this->success_content());
        $this->display();
    }

    private function success_content() {
        $orderNo = I('get.id');
        $Order = D('Order');
        $orderData = $Order->getDetailByOrderNo($orderNo);
        if ($orderData['account_id']!=$this->accountObj->getAccountId()) {
            $this->assign('msg', '该订单不是您的订单，无需支付');
        } else if ($orderData['payment_status_id']==1) {
            $this->assign('msg', '支付成功');
        } else {
            $this->assign('msg', '感谢您在本平台购物');
            $Payment = D('Payment');
            $data['paymentList'] = $Payment->getList();
            $data['order'] = $orderData;
            $this->assign('data',$data);
        }

        $this->assign('changePayment', U('checkout/ajax_change_payment',array('id'=>$orderNo)));

        //检测是否有货
        if ($Order->checkOrderHasGoods($orderNo)) {
            $payBut['url'] = U('payment/index',array('id'=>$orderNo,'code'=>$orderData['payment_code']));
            $payBut['text'] = '立即支付';
            $payBut['status'] = 1;
        } else {
            $payBut['url'] = 'javascript:void()';
            $payBut['text'] = '售罄';
            $payBut['status'] = 0;
        }
        $this->assign('payBut', $payBut);
        return $this->fetch('Checkout/success_content');
    }

    public function ajax_change_payment() {
        $orderNo = I('get.id');
        $Order = D('Order');
        $json = array('success'=>0,'msg'=>'');
        $orderData = $Order->getDetailByOrderNo($orderNo);
        if ($orderData['account_id']!=$this->accountObj->getAccountId()) {
            $json['msg'] = '该订单不是您的订单，无需支付';
        } else if ($orderData['payment_status_id']==1) {
            $json['msg'] = '该订单已支付';
        } else {
            $Payment = D('Payment');
            $data['payment_code'] = I('post.payment_code');
            $data['payment_method'] = $Payment->getPaymentMethodByCode($data['payment_code']);
            $result = $Order->where(array('order_no'=>$orderNo))->data($data)->save();
            if ($result!==false) {
                $json = array('success'=>1,'msg'=>$this->success_content());
            } else {
                $json['msg'] = '服务器异常';
            }
        }
        $this->ajaxReturn($json);
    }
}