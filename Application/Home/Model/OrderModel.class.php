<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class OrderModel extends Model {
    private $accountObj = '';


    public function _initialize () {
        $this->accountObj = \Org\Home\Account::getInstance();
    }

    public function addOrder ($data, $cartInfo) {
        $AccountAddress = D('AccountAddress');
        $Shipping = D('Shipping');
        $Payment = D('Payment');

        //发票信息
        $this->invoice_status = $data['invoice_status'];
        if($data['invoice_status']==1) {
            $this->invoice_title = $data['invoice_title'];
            $this->invoice_type = $data['invoice_type'];
        }
        $this->order_no = $order_no = date("ymdHis").\Org\Util\String::randString(4,1);
        //用户信息
        $this->account_id = $this->accountObj->getAccountId();
        $this->firstname = $this->accountObj->accountInfo['firstname'];
        $this->lastname = $this->accountObj->accountInfo['lastname'];
        $this->email = $this->accountObj->accountInfo['email'];
        $this->telephone = $this->accountObj->accountInfo['telephone'];
        //支付信息
        $this->payment_code = $data['payment_code'];
        $this->payment_method = $Payment->getPaymentMethodByCode($data['payment_code']);
        if ($cartInfo['totals'][($cartInfo['totalRow']-1)] ==0) {
            $this->payment_status_id = 1;
            $this->payment_date = time();
        } else {
            $this->payment_status_id = 0;
        }
        //配送信息
        $addressInfo = $AccountAddress->getDataById($data['address_id']);
        $this->shipping_name = $addressInfo['name'];
        $this->shipping_country = $addressInfo['country_name'];
        $this->shipping_province = $addressInfo['province_name'];
        $this->shipping_city = $addressInfo['city_name'];
        $this->shipping_district = $addressInfo['district_name'];
        $this->shipping_postcode = $addressInfo['zipcode'];
        $this->shipping_country_id = $addressInfo['country'];
        $this->shipping_province_id = $addressInfo['province'];
        $this->shipping_city_id = $addressInfo['city'];
        $this->shipping_district_id = $addressInfo['district'];
        $this->shipping_telephone = $addressInfo['telephone'];
        $this->shipping_address = $addressInfo['address'];
        $this->shipping_address_id = $addressInfo['id'];
        $this->shipping_code = $data['shipping_code'];
        $shippingInfo = $Shipping->getShippingByCode($data['shipping_code']);
        $this->shipping_method = $shippingInfo[0]['name'];

        //其它信息
        $this->comment = $data['comment'];
        $this->total = $cartInfo['totals'][($cartInfo['totalRow']-1)]['price'];
        $this->order_status_id = 1;
        $this->ip = get_client_ip();
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $this->accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $this->order_type = 0;
        $this->date_added = time();
        $this->date_updated = time();
        $this->startTrans();
        $orderId = $this->add();
        if ($orderId) {
            $orderGoods = array();
            foreach ($cartInfo['selectGoods'] as $key=>$val) {
                $orderGoods[$key]['order_id'] = $orderId;
                $orderGoods[$key]['goods_id'] = $val['id'];
                $orderGoods[$key]['product_sn'] = $val['product_sn'];
                $orderGoods[$key]['name'] = $val['name'];
                $orderGoods[$key]['number'] = $val['number'];
                $orderGoods[$key]['price'] = $val['price'];
                $orderGoods[$key]['or_price'] = $val['or_price'];
                $orderGoods[$key]['total'] = $val['price']*$val['number'];
                $orderGoods[$key]['goods_attr'] = $val['attr_value'];
                $orderGoods[$key]['is_gift'] = 0;
            }
            //赠品
            if(!empty($cartInfo['gift'])) {
                foreach ($cartInfo['gift'] as $key=>$val) {
                    $orderGoods[$key]['order_id'] = $orderId;
                    $orderGoods[$key]['goods_id'] = $val['id'];
                    $orderGoods[$key]['product_sn'] = $val['product_sn'];
                    $orderGoods[$key]['name'] = $val['name'];
                    $orderGoods[$key]['number'] = $val['number'];
                    $orderGoods[$key]['price'] = $val['price'];
                    $orderGoods[$key]['or_price'] = $val['or_price'];
                    $orderGoods[$key]['total'] = $val['price']*$val['number'];
                    $orderGoods[$key]['goods_attr'] = $val['attr_value'];
                    $orderGoods[$key]['is_gift'] = 1;
                }
            }

            $status = M('OrderGoods')->addAll($orderGoods);
            if(!$status) {
                $this->rollback();
                return false;
            }
            //订单小计
            $orderTotal = array();
            $orderTotal['order_id'] = $orderId;
            foreach ($cartInfo['totals'] as $val) {
                $orderTotal[$val['code']] = $val['price'];
            }
            $status = M('OrderTotal')->add($orderTotal);
            if(!$status) {
                $this->rollback();
                return false;
            }
            //添加促销记录
            $usePromotions = array();
            if (!empty($cartInfo['usePromotions'])) {
                foreach ($cartInfo['usePromotions'] as $key => $val) {
                    $usePromotions['order_id'] = $orderId;
                    $usePromotions['promotion_id'] = $val['id'];
                    $usePromotions['promotion_name'] = $val['name'];
                    $usePromotions['promotion_type'] = $val['type'];
                    $usePromotions['promotion_price'] = $val['price'];
                    $status = $orderPromotionId = M('OrderPromotions')->add($usePromotions);
                    if(!$status) {
                        $this->rollback();
                        return false;
                    }
                    if (!in_array($val['type'],array(1,5))) {
                        $promotionGoods = array();
                        foreach ($val['goods'] as $k => $v) {
                            $promotionGoods[$k]['order_promotions_id'] = $orderPromotionId;
                            $promotionGoods[$k]['goods_id'] = $v['id'];
                            $promotionGoods[$k]['product_sn'] = $v['product_sn'];
                            $promotionGoods[$k]['number'] = $v['number'];
                        }
                        $OrderPromotionsGoods = M('OrderPromotionsGoods');
                        $status = $OrderPromotionsGoods->addAll($promotionGoods);
                        if(!$status) {
                            $this->rollback();
                            return false;
                        }
                    }
                }
            }
            //添加优惠券记录
            $couponHistory = array();
            if (!empty($cartInfo['useCoupon'])) {
                $couponHistory['order_id'] = $orderId;
                $couponHistory['coupon_id'] = $cartInfo['useCoupon']['id'];
                $couponHistory['account_id'] = $this->accountObj->getAccountId();
                $couponHistory['amount'] = $cartInfo['useCoupon']['amount'];
                $couponHistory['date_added'] = time();
                $CouponHistory = M('CouponHistory');
                $status = $CouponHistory->add($couponHistory);
                if(!$status) {
                    $this->rollback();
                    return false;
                }
                $CouponGoods = array();
                foreach ($cartInfo['useCoupon']['goods'] as $k => $v) {
                    $CouponGoods[$k]['order_id'] = $orderId;
                    $CouponGoods[$k]['coupon_id'] = $cartInfo['useCoupon']['id'];
                    $CouponGoods[$k]['goods_id'] = $v['id'];
                    $CouponGoods[$k]['product_sn'] = $v['product_sn'];
                    $CouponGoods[$k]['number'] = $v['number'];
                }
                if (!empty($CouponGoods)) {
                    $OrderCouponGoods = M('OrderCouponGoods');
                    $status = $OrderCouponGoods->addAll($CouponGoods);
                    if(!$status) {
                        $this->rollback();
                        return false;
                    }
                }

                $sql = "UPDATE __PREFIX__coupon SET used_total=used_total+1,date_updated='".time()."' WHERE id='".$cartInfo['useCoupon']['id']."'";
                $status = $this->execute($sql);
                if(!$status) {
                    $this->rollback();
                    return false;
                }
            }

            //添加用户行为
            $AccountActivity = D('AccountActivity');
            $status = $AccountActivity->addActivity($this->accountObj->getAccountId(),'order');
            if(!$status) {
                $this->rollback();
                return false;
            }
            //修改积分和余额
            $AccountIntegral = D('AccountIntegral');
            if( isset($orderTotal['integral']) && abs($orderTotal['integral'])>0) {
                $AccountIntegralUsed = D('AccountIntegralUsed');
                $AccountIntegralUsedDate = array();
                $AccountIntegralUsedDate['title'] = '订单消费';
                $AccountIntegralUsedDate['use_type'] = 1;
                $AccountIntegralUsedDate['account_id'] = $this->accountObj->getAccountId();
                $AccountIntegralUsedDate['order_id'] = $orderId;
                $AccountIntegralUsedDate['points'] = -100*$orderTotal['integral'];
                $AccountIntegralUsedDate['date_added'] = time();
                $status = $AccountIntegralUsed->add($AccountIntegralUsedDate);
                if(!$status) {
                    $this->rollback();
                    return false;
                }
                $status = $this->accountObj->updateIntegral(-100*$orderTotal['integral']);
                if(!$status) {
                    $this->rollback();
                    return false;
                }
            }
            if (isset($orderTotal['money']) && abs($orderTotal['money'])>0) {
                $AccountTransaction = D('AccountTransaction');
                $AccountTransactionData = array();
                $AccountTransactionData['title'] = '订单使用';
                $AccountTransactionData['type'] = 5;
                $AccountTransactionData['account_id'] = $this->accountObj->getAccountId();
                $AccountTransactionData['order_id'] = $orderId;
                $AccountTransactionData['money'] = $orderTotal['money'];
                $AccountTransactionData['status'] = 1;
                $AccountTransactionData['date_added'] = time();
                $status= $AccountTransaction->add($AccountTransactionData);
                if(!$status) {
                    $this->rollback();
                    return false;
                }
                $status = $this->accountObj->updateMoney($orderTotal['money']);
                echo '13: '.$this->getDbError().'<br/>';
                if(!$status) {
                    $this->rollback();
                    return false;
                }
            }
            //赠送积分
            if ($cartInfo['giveIntegral']>0) {
                $AccountIntegralData = array ();
                $AccountIntegralData['title'] = '消费积分';
                $AccountIntegralData['get_type'] = 1;
                $AccountIntegralData['account_id'] = $this->accountObj->getAccountId();
                $AccountIntegralData['order_id'] = $orderId;
                $AccountIntegralData['start_date'] = date('Y-m-d');
                $AccountIntegralData['end_date'] = date('Y-m-d',strtotime('+1 year'));
                $AccountIntegralData['points'] = $cartInfo['giveIntegral'];
                $AccountIntegralData['status'] = 0; //订单完成后才生效
                $AccountIntegralData['add_user_id'] = 0;
                $AccountIntegralData['date_added'] = time();
                $AccountIntegralData['date_updated'] = time();
                $status = $AccountIntegral->add($AccountIntegralData);
                if(!$status) {
                    $this->rollback();
                    return false;
                }
            }
        }
        $this->commit();
        //保存订单号
        session('order_no',$order_no);
        return $orderId;
    }

    public function getList ($filter = array()) {
        $order = isset($filter['order']) ? $filter['order'] : 'o.date_added DESC, o.id DESC ,o.id DESC';
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('PAGE_SIZE');

        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $condition = " AND ";
        foreach ($filter as $key => $val) {
            if (!empty($val)) {
                $condition .= " {$key}='{$val}' AND ";
            }
        }
        $condition = rtrim(trim($condition), 'AND');

        $sql = "SELECT o.id,o.order_no,o.invoice_status,o.invoice_title,o.invoice_type,
                        o.payment_method,o.payment_code, o.payment_status_id,o.order_status_id,
                        ot.goods_total, ot.shipping, ot.integral, ot.money, ot.coupon,
                        ot.sales_promotion, ot.total,ot.update_total,o.date_added,o.date_updated,
                        os.name order_status,ps.`name` payment_status
                FROM `__PREFIX__order` o
                LEFT JOIN `__PREFIX__order_status` os
                ON o.order_status_id = os.order_status_id
                LEFT JOIN `__PREFIX__payment_status` ps
                ON o.payment_status_id=ps.payment_status_id
                LEFT JOIN `__PREFIX__order_total`  ot
                ON o.id = ot.order_id
                WHERE o.account_id ='{$this->accountObj->getAccountId()}' {$condition}
                ORDER By {$order}
                LIMIT {$start},{$limit}";
        $orderData = $this->query($sql);

        if ( !empty($orderData)){
            $orderIds = array();
            foreach ($orderData as  $val) {
                $orderIds[] = $val['id'];
            }
            $orderIds = implode(',', $orderIds);
            //获取商品
            $sql = "SELECT og.*,g.thumb FROM __PREFIX__order_goods og
                    LEFT JOIN __PREFIX__goods g
                    ON g.id=og.goods_id
                    WHERE og.order_id in($orderIds)
                    ORDER BY og.order_id DESC, og.id DESC";
            $goodsData = $this->query($sql);
            foreach ($orderData as $key => $val) {
                foreach ($goodsData as $v) {
                    if ($val['id'] == $v['order_id']) {
                        $orderData[$key]['goods'][] = $v;
                    }
                }
                if ($val['order_status_id']==4 && (time()-$val['date_updated'])<7*24*3600) {
                    $orderData[$key]['isCanReturn'] = 1;
                } else {
                    $orderData[$key]['isCanReturn'] = 0;
                }
            }
        }
        return $orderData;

    }

    public function getTotal ($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $condition = " AND ";
        foreach ($filter as $key => $val) {
            if (!empty($val)) {
                $condition .= " {$key}='{$val}' AND ";
            }
        }
        $condition = rtrim(trim($condition), 'AND');

        $sql = "SELECT count(*) total
                FROM `__PREFIX__order` o
                LEFT JOIN `__PREFIX__order_status` os
                ON o.order_status_id = os.order_status_id
                LEFT JOIN `__PREFIX__payment_status` ps
                ON o.payment_status_id=ps.payment_status_id
                WHERE o.account_id =1 {$condition}";
        $data = $this->query($sql);
        return $data[0]['total'];
    }

    public function getDetailByOrderNo ($order_no) {
        $sql = "SELECT o.*,ot.goods_total, ot.shipping, ot.integral, ot.money,
                        ot.coupon,ot.sales_promotion, ot.update_total,
                        os.name order_status,ps.`name` payment_status
                FROM `__PREFIX__order` o
                LEFT JOIN `__PREFIX__order_status` os
                ON o.order_status_id = os.order_status_id
                LEFT JOIN `__PREFIX__payment_status` ps
                ON o.payment_status_id=ps.payment_status_id
                LEFT JOIN `__PREFIX__order_total`  ot
                ON o.id = ot.order_id
                WHERE o.order_no = '{$order_no}'
                AND  o.account_id= '{$this->accountObj->getAccountId()}'
                LIMIT 0,1";
        $data = $this->query($sql);
        if (!empty($data)) {
            $sql = "SELECT og.*, g.thumb
                    FROM `__PREFIX__order_goods` og
                    LEFT JOIN `__PREFIX__goods` g
                    ON og.goods_id = g.id
                    WHERE og.order_id = '{$data[0]['id']}'";
            $goodsData = $this->query($sql);
            $data[0]['goods'] = $goodsData;
        }
        return !empty($data) ? $data[0] :$data;
    }

    public function getOrderIdByOrderNo ($order_no) {
        $sql = "SELECT id FROM __PREFIX__order WHERE order_no='{$order_no}'";
        $data = $this->query($sql);
        return empty($data) ? 0 : $data[0]['id'];
    }

    public function getOrderNoByOrderId ($id) {
        $sql = "SELECT order_no FROM __PREFIX__order WHERE id='{$id}'";
        $data = $this->query($sql);
        return empty($data) ? 0 : $data[0]['order_no'];
    }

    public function setOrderPaymentStatus($orderNo,$data) {
        if(empty($orderNo)) {
            return false;
        }
        $result = $this->where(array('order_no'=>$orderNo))->save($data);
        if($data['payment_status_id'] == 1) {
            //修改库存
            $orderObj = new \Org\Common\Order();
            $orderObj->setGoodsNumberByOrderNo($orderNo);
        }
        return $result;
    }

    public function checkOrderHasGoods ($orderNo) {
        $sql = "SELECT og.goods_id,og.product_sn,
                IF(og.product_sn='',g.number-og.number,p.product_number-og.number) number
                FROM `__PREFIX__order` o
                LEFT JOIN __PREFIX__order_goods og
                ON o.id = og.order_id
                LEFT JOIN __PREFIX__goods g
                ON og.goods_id = g.id
                LEFT JOIN __PREFIX__products p
                ON og.product_sn = p.product_sn
                WHERE o.order_no='{$orderNo}' ";
        $goodsData = $this->query($sql);
        $flag = true;
        if(!empty($goodsData)) {
            foreach( $goodsData as $goods ) {
                if ($goods['number']<0) {
                    $flag = false;
                    break;
                }
            }
        } else {
            $flag = false;
        }
        return $flag;
    }

}


?>