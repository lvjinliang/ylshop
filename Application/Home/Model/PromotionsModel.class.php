<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Home\Model;

use Think\Model;

class PromotionsModel extends Model {
    protected $accountObj = null;

    public function _initialize () {
        $this->accountObj = \Org\Home\Account::getInstance();
    }

    /**
     * @function 获取所有促销规则
     * @return mixed
     */
    public function getAllPromotions () {
        $sql = "SELECT *
                FROM __PREFIX__promotions p
                WHERE p.status=1
                AND start_time<=UNIX_TIMESTAMP(CURRENT_DATE())
                AND end_time>=UNIX_TIMESTAMP(CURRENT_DATE())
                ORDER BY sort DESC,id DESC";
        $data = $this->query($sql);
        return $data;
    }

    /**
     * @function 获取每种促销中含有的商品
     * @return array
     */
    public function getPromotionsGoods () {
        $data = array();
        $promotions = $this->getAllPromotions();
        if (empty($promotions)) {
            return $data;
        } else {
            $rank = $this->accountObj->getAccountRank();
            $flag = true;
            foreach ($promotions as $promotion) {
                if ($rank != 0) {
                    //过滤等级
                    if (!in_array($rank, explode(',', $promotion['account_rank']))) {
                        continue;
                    }
                }

                //{1:全场促销,2:部份促销,3:多份促销,4:组合促销,5:买赠促销}
                $act_rule = array();
                switch ($promotion['type']) {
                    case 1:
                        if ($flag) {
                            $flag = false;
                            //$data[] = array('all' => $promotion['id']);
                            $data[$promotion['id']]['goods'] = 'all';
                            $data[$promotion['id']]['detail'] = $promotion;
                            continue 2;
                        }
                        break;
                    case 2:
                        $act_rule = array_unique(explode(',', $promotion['act_rule']));
                        //优惠范围{1:商品,2:类,3:品牌}
                        $Goods = D('Goods');
                        $goods = array();
                        switch ($promotion['act_range']) {
                            case 1:
                                $data[$promotion['id']]['goods'] = $act_rule;
                                break;
                            case 2:
                                foreach ($act_rule as $key => $val) {
                                    $tmp = $Goods->getGoodsIdByCategoryId($key);
                                    foreach ($tmp as $v) {
                                        $goods[] = $v;
                                    }
                                }
                                $data[$promotion['id']]['goods'] = array_unique($goods);
                                break;
                            case 3:
                                foreach ($act_rule as $key => $val) {
                                    $tmp = $Goods->getGoodsIdByBrandId($key);
                                    foreach ($tmp as $v) {
                                        $goods[] = $v;
                                    }
                                }
                                $data[$promotion['id']]['goods'] = array_unique($goods);
                                break;
                            default :
                                break;
                        }
                        break;
                    case 3:
                        //sn*商品数量；
                        $tmp = explode('*', $promotion['act_rule']);
                        $act_rule[] = $tmp[0];
                        $data[$promotion['id']]['goods'] = $act_rule;
                        break;
                    case 4:
                        //sn1*商品数量+sn2*商品数量
                        $tmp = explode('+', $promotion['act_rule']);
                        foreach ($tmp as $v) {
                            $tmp2 = explode('*', $v);
                            $act_rule[] = $tmp2[0];
                        }
                        $data[$promotion['id']]['goods'] = $act_rule;
                        break;
                    case 5:
                        //sn*商品数量；
                        $tmp = explode('+', $promotion['act_rule']);
                        foreach ($tmp as $v) {
                            $tmp2 = explode('*', $v);
                            $act_rule[] = $tmp2[0];
                        }
                        $data[$promotion['id']]['goods'] = $act_rule;
                        break;
                    default :
                        break;
                }

                $data[$promotion['id']]['detail'] = $promotion;

            }
        }

        return $data;
    }

    public function getPromotionsTotal ($goodses, $total) {
        $sales_promotion = 0;
        if (empty($goodses) || $total == 0) {
            return $sales_promotion;
        }
        $promotionsGoods = $this->getPromotionsGoods();
        $gift = array();   //赠品
        $usePromotions = array();
        foreach ($promotionsGoods as $promotionKey => $promotion) {
            //{1:全场促销,2:部份促销,3:多份促销,4:组合促销,5:买赠促销}
            $promotionTotal = 0;
            switch ($promotion['detail']['type']) {
                case 1:
                    if ($total >= $promotion['detail']['min_amount']) {
                        switch ($promotion['detail']['d_type']) {
                            case 1:
                                $sales_promotion += $promotion['detail']['discount'];
                                $promotionTotal = $promotion['detail']['discount'];
                                $total = $total - $promotion['detail']['discount'];
                                break;
                            case 2:
                                $sales_promotion += (1-$promotion['detail']['discount']/100) * $total;
                                $promotionTotal = (1-$promotion['detail']['discount']/100) * $total;
                                $total = $total - (1-$promotion['detail']['discount']/100) * $total;
                                break;
                            default:
                                break;
                        }
                        //记录使用的促销规则
                        $usePromotions[$promotionKey]['name'] = $promotion['detail']['name'];
                        $usePromotions[$promotionKey]['id'] = $promotion['detail']['id'];
                        $usePromotions[$promotionKey]['type'] = $promotion['detail']['type'];
                        $usePromotions[$promotionKey]['goods'] = 'all';
                        $usePromotions[$promotionKey]['price'] = $promotionTotal;

                    }
                case 2:
                    if ($total >= $promotion['detail']['min_amount']) {
                        foreach ($goodses as $key => $goods) {
                            if (in_array($goods['id'], $promotion['goods']) || in_array($goods['id']. '@@' . $goods['product_sn'], $promotion['goods'])) {
                                switch ($promotion['detail']['d_type']) {
                                    case 1:
                                        $sales_promotion += $promotion['detail']['discount']*$goods['number'];
                                        $promotionTotal = $promotion['detail']['discount'];
                                        $total = $total - $promotion['detail']['discount']*$goods['number'];
                                        break;
                                    case 2:
                                        $sales_promotion += (1-$promotion['detail']['discount']/100) * $goods['price']*$goods['number'];
                                        $promotionTotal = (1-$promotion['detail']['discount']/100) * $goods['price']*$goods['number'];
                                        $total = $total - (1-$promotion['detail']['discount']/100) * $goods['price']*$goods['number'];
                                        break;
                                    default:
                                        break;
                                }

                                //记录使用的促销规则
                                $usePromotions[$promotionKey]['name'] = $promotion['detail']['name'];
                                $usePromotions[$promotionKey]['id'] = $promotion['detail']['id'];
                                $usePromotions[$promotionKey]['type'] = $promotion['detail']['type'];
                                $usePromotions[$promotionKey]['price'] = $promotionTotal;
                                $usePromotions[$promotionKey]['goods'][] = array(
                                    'id' => $goods['id'],
                                    'product_sn' => $goods['product_sn'],
                                    'number' => $goods['number']
                                );
                                $goodses[$key]['number'] = 0;
                            }
                        }
                    }
                    break;
                case 3:
                    //sn*商品数量
                    if ($total >= $promotion['detail']['min_amount']) {
                        foreach ($goodses as $key => $goods) {
                            if (in_array($goods['id'], $promotion['goods']) || in_array($goods['id']. '@@' . $goods['product_sn'], $promotion['goods'])) {
                                $tmp = explode('*', $promotion['detail']['act_rule']);
                                if ($goods['number']<$tmp[1]) { //过滤商品数量不足
                                    continue;
                                }
                                $minBeishu = floor($goods['number']/$tmp[1]);
                                switch ($promotion['detail']['d_type']) {
                                    case 1:
                                        $sales_promotion += $promotion['detail']['discount']*$minBeishu;
                                        $promotionTotal = $promotion['detail']['discount']*$minBeishu;
                                        $total = $total - $promotion['detail']['discount']*$minBeishu;
                                        break;
                                    case 2:
                                        $sales_promotion += (1-$promotion['detail']['discount']/100) * $goods['price']*$tmp[1]*$minBeishu;
                                        $promotionTotal = (1-$promotion['detail']['discount']/100) * $goods['price']*$tmp[1]*$minBeishu;
                                        $total = $total - (1-$promotion['detail']['discount']/100) * $goods['price']*$tmp[1]*$minBeishu;
                                        break;
                                    default:
                                        break;
                                }
                                $goodses[$key]['number'] -= $tmp[1]*$minBeishu;
                                //print_r($goodses);
                                //记录使用的促销规则
                                $usePromotions[$promotionKey]['name'] = $promotion['detail']['name'];
                                $usePromotions[$promotionKey]['id'] = $promotion['detail']['id'];
                                $usePromotions[$promotionKey]['type'] = $promotion['detail']['type'];
                                $usePromotions[$promotionKey]['price'] = $promotionTotal;
                                $usePromotions[$promotionKey]['goods'][] = array(
                                    'id' => $goods['id'],
                                    'product_sn' => $goods['product_sn'],
                                    'number' => $tmp[1]*$minBeishu
                                );
                            }
                        }
                    }
                    break;
                case 4:
                    if ($total >= $promotion['detail']['min_amount']) {
                        //sn1*商品数量+sn2*商品数量
                        $rule = explode('+', $promotion['detail']['act_rule']);
                        $ruleGoods = array();
                        foreach ($rule as $rv ) {
                            $tmpRule = explode('*', $rv);
                            $ruleGoods[$tmpRule[0]] = $tmpRule[1];
                        }
                        $tempGoods = array();
                        foreach ($goodses as $key => $goods) {
                            if (empty($goods['product_sn'])) {
                                $tempGoods[$goods['id']]['number'] = $goods['number'];
                                $tempGoods[$goods['id']]['key'] = $key;
                                $tempGoods[$goods['id']]['price'] = $goods['price'];
                            } else {
                                $tempGoods[$goods['id'].'@@'.$goods['product_sn']]['number'] = $goods['number'];
                                $tempGoods[$goods['id'].'@@'.$goods['product_sn']]['key'] = $key;
                                $tempGoods[$goods['id'].'@@'.$goods['product_sn']]['price'] = $goods['price'];
                            }
                        }

                        //判断购买的商品是否完全包含促销规则中的商品
                        if (!empty(array_diff(array_keys($ruleGoods), array_keys($tempGoods)))) {
                            continue 2;
                        }

                        $minBeishu = null;    //购买商品与规则商品的最小倍数
                        foreach ($ruleGoods as $rgk => $rgv) {
                            $tmpBeishu = floor($tempGoods[$rgk]['number']/$rgv);
                            if (empty($minBeishu)) {
                                $minBeishu = $tmpBeishu;
                            }
                            $minBeishu = ($minBeishu-$tmpBeishu)<0?$minBeishu:$tmpBeishu;
                            if ( $minBeishu<1 ) {
                                continue 3; //跳到下一规则
                            }
                        }

                        if ($minBeishu>=1) {
                            $promotionPerPrice = 0;  //每一份组合的价格
                            foreach( $ruleGoods as $rgk => $rgv ) {
                                $promotionPerPrice += $tempGoods[$rgk]['price'] * $rgv;
                                $goodses[$tempGoods[$rgk][$key]]['number'] -= $rgv*$minBeishu;
                                $tempZhStr = explode('@@', $rgk);
                                $usePromotions[$promotionKey]['goods'][] = array(
                                    'id' => $tempZhStr[0],
                                    'product_sn' => isset($tempZhStr[1])?$tempZhStr[1]:'',
                                    'number' => $rgv*$minBeishu
                                );
                            }
                            //记录使用的促销规则
                            $usePromotions[$promotionKey]['name'] = $promotion['detail']['name'];
                            $usePromotions[$promotionKey]['id'] = $promotion['detail']['id'];
                            $usePromotions[$promotionKey]['type'] = $promotion['detail']['type'];


                            switch ($promotion['detail']['d_type']) {
                                case 1:
                                    $sales_promotion += $promotion['detail']['discount']*$minBeishu;
                                    $promotionTotal = $promotion['detail']['discount']*$minBeishu;
                                    $total = $total - $promotion['detail']['discount']*$minBeishu;
                                    break;
                                case 2:
                                    $sales_promotion += (1-$promotion['detail']['discount']/100) * $promotionPerPrice*$minBeishu;
                                    $promotionTotal = (1-$promotion['detail']['discount']/100) * $promotionPerPrice*$minBeishu;
                                    $total = $total - (1-$promotion['detail']['discount']/100) * $promotionPerPrice*$minBeishu;
                                    break;
                                default:
                                    break;
                            }
                            $usePromotions[$promotionKey]['price'] = $promotionTotal;
                        }
                    }

                    break;
                case 5:
                    //sn1*商品数量+sn2*商品数量
                    if ($total >= $promotion['detail']['min_amount']) {
                        //sn1*商品数量+sn2*商品数量
                        $rule = explode('+', $promotion['detail']['act_rule']);
                        $ruleGoods = array();
                        foreach ($rule as $rv ) {
                            $tmpRule = explode('*', $rv);
                            $ruleGoods[$tmpRule[0]] = $tmpRule[1];
                        }
                        $tempGoods = array();
                        foreach ($goodses as $key => $goods) {
                            if (empty($goods['product_sn'])) {
                                $tempGoods[$goods['id']]['number'] = $goods['number'];
                                $tempGoods[$goods['id']]['key'] = $key;
                                $tempGoods[$goods['id']]['price'] = $goods['price'];
                            } else {
                                $tempGoods[$goods['id'].'@@'.$goods['product_sn']]['number'] = $goods['number'];
                                $tempGoods[$goods['id'].'@@'.$goods['product_sn']]['key'] = $key;
                                $tempGoods[$goods['id'].'@@'.$goods['product_sn']]['price'] = $goods['price'];
                            }
                        }

                        //判断购买的商品是否完全包含促销规则中的商品
                        if (!empty(array_diff(array_keys($ruleGoods), array_keys($tempGoods)))) {
                            continue 2;
                        }

                        $minBeishu = null;    //购买商品与规则商品的最小倍数
                        foreach ($ruleGoods as $rgk => $rgv) {
                            $tmpBeishu = floor($tempGoods[$rgk]['number']/$rgv);
                            if (empty($minBeishu)) {
                                $minBeishu = $tmpBeishu;
                            }
                            $minBeishu = ($minBeishu-$tmpBeishu)<0?$minBeishu:$tmpBeishu;
                            if ( $minBeishu<1 ) {
                                continue 3; //跳到下一规则
                            }
                        }

                        if ($minBeishu>=1) {
                            foreach( $ruleGoods as $rgk => $rgv ) {
                                $goodses[$tempGoods[$rgk][$key]]['number'] -= $rgv*$minBeishu;
                                $tempZhStr = explode('@@', $rgk);
                                $usePromotions[$promotionKey]['goods'][] = array(
                                    'id' => $tempZhStr[0],
                                    'product_sn' => isset($tempZhStr[1])?$tempZhStr[1]:'',
                                    'number' => $rgv*$minBeishu
                                );
                            }
                            //记录使用的促销规则
                            $usePromotions[$promotionKey]['name'] = $promotion['detail']['name'];
                            $usePromotions[$promotionKey]['id'] = $promotion['detail']['id'];
                            $usePromotions[$promotionKey]['type'] = $promotion['detail']['type'];
                            $usePromotions[$promotionKey]['goods'] = 'gift';

                            //添加赠品
                            $giftRule = explode('+',  $promotion['detail']['gift_rule']);
                            $giftGoods = array();
                            foreach ($giftRule as $grv ) {
                                $tmpGiftRule = explode('*', $grv);
                                $tmpGift = explode('@@',$tmpGiftRule[0]);
                                $giftGoods['goods_id'] = $tmpGift[0];
                                $giftGoods['product_sn'] = isset($tmpGift[1]) ? $tmpGift[1]: '';
                                $giftGoods['number'] = $tmpGiftRule[1];
                                $giftGoods['checked'] = 1;
                                $gift[] = $giftGoods;
                            }
                        }
                    }
                    break;
                default :
                    break;
            }
        }
        cookie('checkout.gift',serialize($gift));
        cookie('checkout.usePromotions',serialize($usePromotions));
        //$sales_promotion = $sales_promotion>$total?$total:$sales_promotion;
        return $sales_promotion;

    }


}


?>