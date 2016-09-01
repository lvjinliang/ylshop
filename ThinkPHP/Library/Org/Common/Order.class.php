<?php
namespace Org\Common;

class Order {
    private $Order = '';
    private $OrderAction = '';

    public function __construct() {
        $this->Order = M('Order');
        $this->OrderAction = M('OrderAction');
    }

    public function updateOrderStatus($orderId, $orderStatusId) {
        $data['id'] = $orderId;
        $data['order_status_id'] = $orderStatusId;
        $data['date_updated'] = time();
        $result = $this->Order->data($data)->save();
        return $result;
    }

    /**
     * @function 添加添订操作记录
     * @param $data
     * @return mixed
     */
    public function addOrderAction($data) {
        $data['date_added'] = time();
        $result = $this->OrderAction->data($data)->add();
        return $result;
    }

    /**
     * @function 添加订单调价记录
     * @param $data
     * @return mixed
     */
    public function addOrderTotalAction($data) {
        $data['date_added'] = time();
        $result = M('OrderTotalAction')->data($data)->add();
        return $result;
    }

    public function updateOrder($data, $orderInfo) {
        $AreaObj = new \Org\Common\Area ();
        $data['shipping_province_id'] = $data['province'];
        $data['shipping_city_id'] = $data['city'];
        $data['shipping_districts_id'] = $data['districts'];
        unset($data['province']);
        unset($data['city']);
        unset($data['districts']);
        $data['shipping_province'] = $AreaObj->getNameById($data['shipping_province_id']);
        $data['shipping_city'] = $AreaObj->getNameById($data['shipping_city_id']);
        $data['shipping_districts'] = $AreaObj->getNameById($data['shipping_districts_id']);
        $data['date_updated'] = time();
        if ((float)$orderInfo['update_total'] != (float)$data['update_total']) {
            $data['total'] = ($orderInfo['goods_total'] + $orderInfo['shipping'] + $orderInfo['integral'] + $orderInfo['money'] + $orderInfo['coupon'] + $orderInfo['sales_promotion'] + $data['update_total']);
        }
        $result = $this->Order->data($data)->save(); //修改订单

        $orderActionData['order_id'] = $data['id'];
        $orderActionData['action_user'] = session('user.name');
        $orderActionData['order_status_id'] = $data['order_status_id'];
        $orderActionData['payment_status_id'] = $orderInfo['order_status_id'];
        $orderActionData['action_note'] = $data['action_note'];
        $this->addOrderAction($orderActionData); //添加订单记录
        unset($orderActionData);

        if ((float)$orderInfo['update_total'] != (float)$data['update_total']) {
            $orderTotalActionData['order_id'] = $data['id'];
            $orderTotalActionData['action_user'] = session('user.name');
            $orderTotalActionData['update_total'] = $data['update_total'];
            $orderTotalActionData['update_total_note'] = $data['update_total_note'];
            $this->addOrderTotalAction($orderTotalActionData); //添加订单调价记录
            unset($orderTotalActionData);

            $orderTotalData['total'] = $data['total'];
            $orderTotalData['update_total'] = $data['update_total'];
            M('OrderTotal')->where(array('order_id' => $data['id']))->data($orderTotalData)->save();
            unset($orderTotalData);
        }

        return $result;

    }

    public function getDataById($id) {
        $data = $this->Order->field('o.*,ot.goods_total, ot.shipping, ot.integral, ot.money,
                        ot.coupon,ot.sales_promotion, ot.update_total, a.username,
                        os.name order_status,ps.`name` payment_status')
            ->alias('o')
            ->join('LEFT JOIN __ORDER_STATUS__ os ON o.order_status_id = os.order_status_id')
            ->join('LEFT JOIN __PAYMENT_STATUS__ ps ON o.payment_status_id=ps.payment_status_id')
            ->join('LEFT JOIN __ORDER_TOTAL__ ot ON o.id = ot.order_id')
            ->join('LEFT JOIN __ACCOUNT__ a ON a.id = o.account_id')
            ->where(array('o.id' => $id))
            ->find();

        if (!empty($data)) {
            $data['goods'] = M('OrderGoods')->where(array('order_id' => $data['id']))->select();
            $data['orderAction'] = M('OrderAction')
                ->field('o.*,os.name order_status,ps.`name` payment_status')
                ->alias('o')
                ->join('LEFT JOIN __ORDER_STATUS__ os ON o.order_status_id = os.order_status_id')
                ->join('LEFT JOIN __PAYMENT_STATUS__ ps ON o.payment_status_id=ps.payment_status_id')
                ->where(array('order_id' => $data['id']))
                ->order(array('id' => 'ASC'))
                ->select();
        }
        return $data;
    }

    /**
     * 取消订单
     */
    public function cancel($id) {
        $result = false;
        $orderInfo = $this->getDataById($id);
        //判断订单状态是否允许取消
        if (in_array($orderInfo['order_status_id'], array(1, 2))) {
            $result = $this->updateOrderStatus($id, 7);
            if ($result !== false ) {
                $this->back_payment($orderInfo,7);
            }

        }
        return $result;
    }

    /**
     * 退货订单
     */
    public function return_order($id) {
        $result = false;
        $orderInfo = $this->getDataById($id);
        //判断订单状态是否允许退货
        if ($orderInfo['order_status_id']==8) {
            $result = $this->updateOrderStatus($id, 9);
            if ($result !== false ) {
                $this->back_payment($orderInfo,8);
            }

        }
        return $result;
    }

    //退款处理
    private function back_payment ($orderInfo, $type=7) {
        //积分支付不为零
        if ($orderInfo['integral'] != 0) {
            $AccountIntegral = M('AccountIntegral');
            $AccountIntegralData = array();
            if ($type == 7) {
                $AccountIntegralData['title'] = '取消订单';
                $AccountIntegralData['use_type'] = 4;
            } else if ($type == 8) {
                $AccountIntegralData['title'] = '退货';
                $AccountIntegralData['use_type'] = 5;
            }

            $AccountIntegralData['account_id'] = $orderInfo['account_id'];
            $AccountIntegralData['order_id'] = $orderInfo['id'];
            $AccountIntegralData['start_date'] = date('Y-m-d');
            $AccountIntegralData['end_date'] = date('Y-m-d',strtotime('+1 year'));
            $AccountIntegralData['points'] = -100*$orderInfo['integral'];
            $AccountIntegralData['status'] = 1; //订单完成后才生效
            $AccountIntegralData['add_user_id'] = session('user.id');
            $AccountIntegralData['date_added'] = time();
            $AccountIntegralData['date_updated'] = time();
            $AccountIntegral->add($AccountIntegralData);
        }
        //余额支付不为零
        if ($orderInfo['money']!=0) {
            $AccountTransaction = D('AccountTransaction');
            $AccountTransactionData = array();
            if ($type == 7) {
                $AccountTransactionData['title'] = '取消订单';
                $AccountTransactionData['type'] = 3;
            } else if ($type == 8) {
                $AccountIntegralData['title'] = '退货';
                $AccountIntegralData['use_type'] = 4;
            }
            $AccountTransactionData['account_id'] = $orderInfo['account_id'];
            $AccountTransactionData['order_id'] = $orderInfo['id'];
            $AccountTransactionData['money'] = -1*$orderInfo['money'];
            $AccountTransactionData['status'] = 1;
            $AccountTransactionData['date_added'] = time();
            $AccountTransaction->add($AccountTransactionData);
            M('Account')->where(array('id'=>$orderInfo['account_id']))->setInc('money', -1*$orderInfo['money']);
        }

        //退支付款
        if ($orderInfo['total'] != 0 && $orderInfo['payment_status_id']==1) {
            //待开发……

        }
    }

    public function setGoodsNumberByOrderNo ($orderNo) {
        $sql = "SELECT og.*
                FROM `__PREFIX__order` o
                LEFT JOIN __PREFIX__order_goods og
                ON o.id = og.order_id
                WHERE o.order_no='{$orderNo}'";
        $goodsData = $this->Order->query($sql);
        if(!empty($goodsData)) {
            foreach( $goodsData as $goods ) {
                if(empty($goods['product_sn'])) {
                    $uSql = "UPDATE __PREFIX__goods SET number=number-{$goods['number']}";
                    $this->Order->execute($uSql);
                } else {
                    $uSql = "UPDATE __PREFIX__products SET number=number-{$goods['number']}";
                    $this->Order->execute($uSql);
                }
            }
        }
    }


}