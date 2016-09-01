<?php
/**
 * User: 良子
 * Date: 16-05-06
 */

namespace Home\Model;

use Think\Model;

class CouponModel extends Model {
    protected $accountObj = null;

    public function _initialize () {
        $this->accountObj = \Org\Home\Account::getInstance();
    }

    /**
     * @function 获取所有促销规则
     * @return mixed
     */
    public function getAllCoupon ($goodses, $total) {
        $sql = "SELECT c.*
                FROM `__PREFIX__coupon` c
                LEFT JOIN `__PREFIX__coupon_account` ca
                ON c.id = ca.coupon_id
                WHERE (ca.account_id = '{$this->accountObj->getAccountId()}' OR ISNULL(ca.account_id ))
                AND c.status = 1
                AND c.total-c.used_total>0
                AND c.start_time<=UNIX_TIMESTAMP(CURRENT_DATE())
                AND c.end_time>=UNIX_TIMESTAMP(CURRENT_DATE())
                AND NOT EXISTS (
                    SELECT 1 FROM `__PREFIX__coupon_history`
                    WHERE coupon_id=c.id AND account_id=ca.account_id
                )
                ORDER BY c.sort DESC,c.id DESC";
        $data = $this->query($sql);
        if (!empty($data)) {
            $Goods = D('Goods');
            foreach ($data as $key => $val) {
                if ($total<$val['min_amount']) {
                    unset($data[$key]);
                    continue;
                }
                if (empty($val['act_rule'])) {
                    continue;
                }
                $tempRule = explode(',', $val['act_rule']);

                switch ($val['act_range']) {
                    case 1:
                        $flag = false;
                        foreach ($goodses as $k => $goods) {
                            if (in_array($goods['id'], $tempRule) || in_array($goods['id']. '@@' . $goods['product_sn'], $tempRule)) {
                                $flag = true;
                            }
                        }
                        if (!$flag) {
                            unset($data[$key]);
                        }
                        break;
                    case 2:
                        $ruleGoods = array();
                        foreach ($tempRule as $k => $v) {
                            $tmp = $Goods->getGoodsIdByCategoryId($v);
                            $ruleGoods = $ruleGoods + $tmp;
                            /*foreach ($tmp as $vv) {
                                $ruleGoods[] = $v;
                            }*/
                        }
                        $flag = false;
                        foreach ($goodses as $k => $goods) {
                            if (in_array($goods['id'], $ruleGoods) ) {
                                $flag = true;
                            }
                        }
                        if (!$flag) {
                            unset($data[$key]);
                        }
                        break;
                    case 3:
                        $ruleGoods = array();
                        foreach ($tempRule as $k => $v) {
                            $tmp = $Goods->getGoodsIdByBrandId($v);
                            $ruleGoods = $ruleGoods + $tmp;
                        }
                        $flag = false;
                        foreach ($goodses as $k => $goods) {
                            if (in_array($goods['id'], $ruleGoods) ) {
                                $flag = true;
                            }
                        }
                        if (!$flag) {
                            unset($data[$key]);
                        }
                        break;
                    default :
                        break;
                }
            }
        }
        return $data;
    }

    public function getCouponTotal ($id, $goodses, $total) {
        $coupon_price = 0;
        $useCoupon = array();
        $couponInfo = $this->getCouponInfo($id);
        if (empty($couponInfo)) {
            return $coupon_price;
        }
        if ($total<$couponInfo['min_amount']) {
            return $coupon_price;
        }
        if (empty($couponInfo['act_rule'])) {
            switch ($couponInfo['d_type']) {
                case 1:
                    $coupon_price = $couponInfo['discount'];
                    break;
                case 2:
                    $coupon_price = (1-$couponInfo['discount']) * $total;
                    break;
                default:
                    break;
            }
            return $coupon_price;
        }
        $tempRule = explode(',', $couponInfo['act_rule']);
        $Goods = D('Goods');
        switch ($couponInfo['act_range']) {
            case 1:
                foreach ($goodses as $k => $goods) {
                    if (in_array($goods['id'], $tempRule) || in_array($goods['id']. '@@' . $goods['product_sn'], $tempRule)) {
                        switch ($couponInfo['d_type']) {
                            case 1:
                                $coupon_price += $couponInfo['discount']*$goods['number'];
                                break;
                            case 2:
                                $coupon_price += (1-$couponInfo['discount']/100) * $goods['price']*$goods['number'];
                                break;
                            default:
                                break;
                        }
                        //记录使用的coupon规则
                        $useCoupon['id'] = $id;
                        $useCoupon['amount'] = $coupon_price;
                        $useCoupon['goods'][] = array(
                            'id' => $goods['id'],
                            'product_sn' => $goods['product_sn'],
                            'number' => $goods['number']
                        );
                    }

                }

                break;
            case 2:
                $ruleGoods = array();
                foreach ($tempRule as $k => $v) {
                    $tmp = $Goods->getGoodsIdByCategoryId($v);
                    $ruleGoods = $ruleGoods + $tmp;
                }
                foreach ($goodses as $k => $goods) {
                    if (in_array($goods['id'], $ruleGoods)) {
                        switch ($couponInfo['d_type']) {
                            case 1:
                                $coupon_price += $couponInfo['discount']*$goods['number'];
                                break;
                            case 2:
                                $coupon_price += (1-$couponInfo['discount']/100) * $goods['price']*$goods['number'];
                                break;
                            default:
                                break;
                        }
                        //记录使用的coupon规则
                        $useCoupon['id'] = $id;
                        $useCoupon['amount'] = $coupon_price;
                        $useCoupon['goods'][] = array(
                            'id' => $goods['id'],
                            'product_sn' => $goods['product_sn'],
                            'number' => $goods['number']
                        );
                    }
                }
                break;
            case 3:
                $ruleGoods = array();
                foreach ($tempRule as $k => $v) {
                    $tmp = $Goods->getGoodsIdByBrandId($v);
                    $ruleGoods = $ruleGoods + $tmp;
                }
                foreach ($goodses as $k => $goods) {
                    if (in_array($goods['id'], $ruleGoods)) {
                        switch ($couponInfo['d_type']) {
                            case 1:
                                $coupon_price += $couponInfo['discount']*$goods['number'];
                                break;
                            case 2:
                                $coupon_price += (1-$couponInfo['discount']/100) * $goods['price']*$goods['number'];
                                break;
                            default:
                                break;
                        }
                        //记录使用的coupon规则
                        $useCoupon['id'] = $id;
                        $useCoupon['amount'] = $coupon_price;
                        $useCoupon['goods'][] = array(
                            'id' => $goods['id'],
                            'product_sn' => $goods['product_sn'],
                            'number' => $goods['number']
                        );
                    }

                }
                break;
        }
        cookie('checkout.useCoupon',serialize($useCoupon));
        $coupon_price = $coupon_price>$total?$total:$coupon_price;
        return $coupon_price;
    }

    public function getCouponInfo ($id) {
        $sql = "SELECT c.*
                FROM `__PREFIX__coupon` c
                LEFT JOIN `__PREFIX__coupon_account` ca
                ON c.id = ca.coupon_id
                WHERE c.id='{$id}'
                AND (ca.account_id = '{$this->accountObj->getAccountId()}' OR ISNULL(ca.account_id ))
                AND c.status = 1;
                AND c.total-c.used_total>0
                AND c.start_time<=UNIX_TIMESTAMP(CURRENT_DATE())
                AND c.end_time>=UNIX_TIMESTAMP(CURRENT_DATE())
                AND NOT EXISTS (
                    SELECT 1 FROM `__PREFIX__coupon_history`
                    WHERE coupon_id=c.id AND account_id=ca.account_id
                )
                LIMIT 0,1";
        $data = $this->query($sql);
        return empty($data)?array():$data[0];
    }


}


?>