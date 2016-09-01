<?php
namespace Home\Event;

use Think\Controller;

class CartEvent extends Controller {

    /**
     * @param $Cart               购物车对象
     * @param string $type        请求类型cart|checkout
     * @param array $selectGoods  购物车中选择的商品
     * @param bool $hasSelected   是否需要根据购物车中选中的商品，改变购物车的选中状态
     * @return mixed
     */
    public function cart_list($Cart, $type = 'cart', $selectGoods = array(), $hasSelected = false) {
        $accountObj = \Org\Home\Account::getInstance();
        $Goods = D('Goods');
        $data['goods'] = $Goods->getGoodsByCart($Cart, $selectGoods, $hasSelected);
        $goodses = array();      //购物车已选中的商品
        foreach ($data['goods'] as $val) {
            if ($val['checked'] == 1) {
                $goodses[] = $val;
            }
        }

        $data['selectGoods'] = $goodses;
        $data['canUseIntegral'] = 0;
        $data['giveIntegral'] = 0;
        foreach ($data['selectGoods'] as $key => $val) {
            if ($val['integral'] == -1) {
                $data['canUseIntegral'] += (int)($val['price'] * 100);
            } else {
                $data['canUseIntegral'] += (int)$val['integral'];
            }

            if ($val['give_integral']==-1) {
                $data['giveIntegral'] += (int)$val['price'];
            } else {
                $data['giveIntegral'] += (int)$val['give_integral'];
            }
        }

        $data['error'] = array();
        if ($type == 'checkout') {
            //处理积分
            $payIntegral = I('post.pay_integral');
            $payIntegral = (int)$payIntegral;
            $data['use_pay_integral'] = $payIntegral;
            if (!empty($payIntegral)) {
                if ($payIntegral > $accountObj->getIntergral()) {
                    $data['error']['pay_integral'] = '您积分不足';
                }
                if ($payIntegral > $data['canUseIntegral']) {
                    $data['error']['pay_integral'] = '此订单最多可用' . $data['canUseIntegral'] . '积分';
                }
            }
            //处理余额
            $money = I('post.money');
            $money = (int)$money;
            $data['use_money'] = $money;
            if (!empty($money)) {
                if ($money > $accountObj->accountInfo['money']) {
                    $data['error']['money'] = '您余额不足';
                }
            }

            $AccountAddress = D('AccountAddress');
            $Shipping = D('Shipping');
            $Payment = D('Payment');
            $addressId = I('post.address_id');
            if (empty($addressId)) {
                $addressId = session('?checkout.addressId') ? session('checkout.addressId') : '';
            }
            $shippingCode = I('post.shipping_code');
            if (empty($shippingCode)) {
                $shippingCode = session('?checkout.shippingCode') ? session('checkout.shippingCode') : '';
            }
            $paymentCode = I('post.payment_code');
            if (empty($paymentCode)) {
                $paymentCode = session('?checkout.paymentCode') ? session('checkout.paymentCode') : '';
            }

            if (empty($addressId)) {
                $data['error']['addressId'] = '请选地址';
            } else {
                $addressInfo = $AccountAddress->getDataById($addressId);
                if (empty($addressInfo) || $addressInfo['account_id'] != $accountObj->getAccountId()) {
                    $data['error']['addressId'] = '非法操作，请选择地址';
                }
            }

            if (empty($shippingCode)) {
                $data['error']['shippingCode'] = '请选配送方式';
            } else {
                if (!$Shipping->checkShippingCode($shippingCode)) {
                    $data['error']['shippingCode'] = '非法操作，请选配送方式';
                }
            }

            if (empty($paymentCode)) {
                $data['error']['paymentCode'] = '请选支付方式';
            } else {
                if (!$Payment->checkPaymentCode($paymentCode)) {
                    $data['error']['paymentCode'] = '非法操作，请选支付方式';
                }
            }

            session('checkout.addressId', $addressId);
            session('checkout.shippingCode', $shippingCode);
            session('checkout.paymentCode', $paymentCode);

            $invoice_status = I('post.invoice_status');
            $invoice_status = empty($invoice_status) ? 0 : 1;
            if (!empty($invoice_status)) {
                $invoice_title = I('post.invoice_title');
                if (empty($invoice_title)) {
                    $data['error']['invoice_title'] = '请填写发票抬头';
                }
            }
        }

        if (count($goodses)==0) {
            $data['error']['goods'] = '没有选中商品';
        } else {
            foreach($goodses as $val) {
                if ($val['disabled'] == 1) {
                    $data['error']['goods'] = '含有缺货或下架商品';
                    break;
                }
            }
        }

        $data['totals'] = $Goods->getCheckoutTotal($goodses, $type, $data['error']);
        $data['totalRow'] = count($data['totals']);

        //赠品处理
        $gift = cookie('checkout.gift') ? unserialize(cookie('checkout.gift')) : '';
        cookie('checkout.gift',null);
        $data['gift'] = $Goods->getGiftGoods($gift);
        if (!empty($data['gift'])) {
            //重置赠品是否有库存
            foreach ($data['gift'] as $key => $val) {
                foreach ($goodses as $k => $v) {
                    if ($val['number']+$v['number']>$val['kucun']) {
                        $data['gift'][$key]['has_number'] = 0;
                        $data['gift'][$key]['disabled'] = 1;
                        $data['error']['goods'] = '赠品缺货或下架';
                        continue 2;
                    }
                }
            }
        }
        $data['usePromotions'] = cookie('checkout.usePromotions') ? unserialize(cookie('checkout.usePromotions')) : '';
        cookie('checkout.usePromotions',null);
        $data['couponList'] = cookie('checkout.couponList') ? unserialize(cookie('checkout.couponList')) : '';
        cookie('checkout.couponList',null);
        $data['useCoupon'] = cookie('checkout.useCoupon') ? unserialize(cookie('checkout.useCoupon')) : '';
        cookie('checkout.useCoupon',null);
        return $data;
    }
}