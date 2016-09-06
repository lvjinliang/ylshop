<?php
/**
 * User: 良子
 * Date: 16-03-02
 */

namespace Home\Model;

use Think\Model;

class GoodsModel extends Model {

    public function getGoodsIdByPositionId($positionId, $size = 10) {
        $sql = "SELECT pg.goods_id FROM __PREFIX__position_goods pg
                LEFT JOIN __PREFIX__goods g
                ON pg.goods_id = g.id
                WHERE pg.position_id = '{$positionId}'
                AND g.is_on_sale=1
                AND g.is_alone_sale=1
                ORDER BY g.sort DESC, g.id DESC
                LIMIT 0,{$size}";
        $data = $this->query($sql);
        $goodsId = array();
        if ($data) {
            foreach ($data as $key => $val) {
                $goodsId[] = $val['goods_id'];
            }
        }
        return $goodsId;
    }

    /**
     * @author 良子
     * @param $ids string|array
     * @return array
     */
    public function getGoodsByIds($ids) {
        if (empty($ids)) {
            return array();
        }
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }
        $sql = "SELECT g.id, g.name , g.thumb,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),g.promote_price, g.price) price,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),price, null) or_price
                FROM __PREFIX__goods g
                WHERE g.id  in({$ids})
                AND g.is_on_sale=1
                AND g.is_alone_sale=1";
        $query = $this->query($sql);
        $ids = explode(',', $ids);
        //按传入id顺序取
        $data = array();
        foreach ($ids as $id) {
            foreach ($query as $val) {
                if ($val['id'] == $id) {
                    $data[] = $val;
                    break;
                }
            }
        }
        return $data;
    }

    public function getGoodsById($id) {
        if (empty($id)) {
            return array();
        }
        $sql = "SELECT g.*,gc.category_id,b.name brand_name,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),g.promote_price, g.price) price,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),price, null) or_price
                FROM __PREFIX__goods g
                LEFT JOIN __PREFIX__goods_category gc
                ON g.id = gc.goods_id
                LEFT JOIN __PREFIX__brand b
                ON g.brand_id = b.id
                WHERE g.id='{$id}'
                AND g.is_on_sale=1
                AND g.is_alone_sale=1
                ORDER BY gc.is_primary DESC
                LIMIT 0,1";
        $query = $this->query($sql);
        $data = $query[0];
        if (empty($data)) {
            return array();
        }
        if (!empty($data['promote_start_date'])) {
            $data['promote_start_date'] = date('Y-m-d', $data['promote_start_date']);
        }

        if (!empty($data['promote_end_date'])) {
            $data['promote_end_date'] = date('Y-m-d', $data['promote_end_date']);
        }
        $data['old_price'] = $data['price'];
        $data['old_or_price'] = $data['or_price'];
        //相册
        $sql = "SELECT gg.url, gg.title
                FROM __PREFIX__goods_gallery gg
                WHERE gg.goods_id = '{$id}'
                ORDER BY gg.sort DESC ,gg.id DESC ";
        $data['gallerys'] = $this->query($sql);
        //属性
        if (!empty($data['goods_type_id'])) {
            $Attribute = D('Attribute');
            $attr = $Attribute->getAttributeByTypeId($data['goods_type_id']);
            $goodsAttr = $this->getGoodsAttrByGoodsId($id);
            foreach ($goodsAttr as $key => $val) {
                if ($attr[$val['attr_id']]['type'] == 1) {
                    $data['attrOnly'][$attr[$val['attr_id']]['name']] = $val;
                } else {
                    $data['attrSelect'][$attr[$val['attr_id']]['name']][] = $val;
                }
            }
        }
        $data['products'] = $this->getProductByGoodsId($id);

        //价格处理
        $attrPrice = 0;
        $attrValue = array();
        if (!empty($data['attrSelect'])) {
            foreach ($data['attrSelect'] as $key => $val) {
                $attrPrice = $attrPrice + $val[0]['attr_price']; //属性价格
                $attrValue[] = $val[0]['attr_value']; //初始化属性
            }
            $data['price'] = $data['price'] + $attrPrice;
            if (!empty($data['or_price'])) {
                $data['or_price'] = $data['or_price'] + $attrPrice;
            }
            $data['number'] = 0;
        }
        //product处理
        $data['product_sn'] = '';
        if (!empty($data['products']) && !empty($attrValue)) {
            foreach ($data['products'] as $key => $val) {
                $defaultAttr = explode(',', $key);
                if (count(array_intersect($defaultAttr, $attrValue)) == count($defaultAttr)) {
                    $data['product_sn'] = $val['product_sn'];
                    $data['number'] = $val['product_number'];
                }
            }
        }

        return $data;
    }


    public function getProductByGoodsId($id) {
        $sql = "SELECT p.attr_ids, p.attr_value, p.product_sn ,p.product_number
                FROM __PREFIX__products p
                WHERE p.goods_id='{$id}'";
        $query = $this->query($sql);
        $data = array();
        foreach ($query as $val) {
            $data[$val['attr_value']] = $val;
        }
        return $data;
    }

    public function getGoodsAttrByGoodsId($id) {
        $sql = "SELECT ga.attr_id, ga.attr_value, ga.attr_price
                FROM __PREFIX__goods_attr ga
                WHERE ga.goods_id='{$id}'";
        $data = $this->query($sql);
        return $data;
    }

    public function getGoodsIdByFilterAttr($filterAttr) {
        $goodsIds = array();
        if (!empty($filterAttr)) {
            $table = array();
            $where = array();
            $i = 0;
            foreach ($filterAttr['attr_id'] as $key => $val) {
                if (!empty($filterAttr['attr_value'][$key])) {
                    $i++;
                    $table[] = "__PREFIX__goods_attr ga{$i}";
                    $where[] = "ga{$i}.attr_id = '{$val}' AND ga{$i}.attr_value='{$filterAttr['attr_value'][$key]}' ";
                }
            }
            if (count($table) > 0) {
                $sql = "SELECT ga1.goods_id FROM " . implode(',', $table);
                if (count($table) > 1) {
                    for ($i = 2; $i <= count($table); $i++) {
                        $where[] = "ga{$i}.goods_id = ga" . ($i - 1) . ".goods_id";
                    }
                }
                $sql .= " WHERE " . implode(' AND ', $where);
            } else {
                $sql = "";
            }
            if (!empty($sql)) {
                $data = $this->query($sql);
                foreach ($data as $val) {
                    $goodsIds[] = $val['goods_id'];
                }
            }
        }
        return $goodsIds;
    }

    public function getGoodsByCategoryId($categoryId, $filter = array(), $fliterGoodsIds = array()) {
        $Category = D('Category');
        $categoryId = $Category->getChildrenIDbyId($categoryId);
        $categoryId = implode(',', $categoryId);
        $order = isset($filter['order']) ? $filter['order'] : 'gc.is_primary DESC, g.sort DESC ,g.id DESC';
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
        if (!empty($fliterGoodsIds)) {
            $condition .= " AND g.id in(" . implode(',', $fliterGoodsIds) . ")";
        }
        $sql = "SELECT g.id, g.name , g.thumb,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),g.promote_price, g.price) price,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),price, null) or_price
                FROM __PREFIX__goods_category gc
                LEFT JOIN __PREFIX__goods g
                ON gc.goods_id = g.id
                WHERE gc.category_id IN ($categoryId)
                AND g.is_on_sale = 1
                AND g.is_alone_sale = 1
                {$condition}
                ORDER BY {$order}
                Limit {$start}, {$limit}";
        $data = $this->query($sql);
        return $data;
    }

    public function getTotalByCategoryId($categoryId, $filter = array(), $fliterGoodsIds = array()) {
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
        if (!empty($fliterGoodsIds)) {
            $condition .= " AND g.id in(" . implode(',', $fliterGoodsIds) . ")";
        }
        $Category = D('Category');
        $categoryId = $Category->getChildrenIDbyId($categoryId);
        $categoryId = implode(',', $categoryId);
        $sql = "SELECT count(*) total FROM __PREFIX__goods_category gc
                LEFT JOIN __PREFIX__goods g
                ON gc.goods_id = g.id
                WHERE gc.category_id IN ($categoryId)
                AND g.is_on_sale = 1
                AND g.is_alone_sale = 1 {$condition} ";
        $data = $this->query($sql);
        return $data[0]['total'];
    }

    public function getGoodsIdByCategoryId($categoryId) {
        $Category = D('Category');
        $categoryId = $Category->getChildrenIDbyId($categoryId);
        $categoryId = implode(',', $categoryId);
        $sql = "SELECT g.id
                FROM __PREFIX__goods_category gc
                LEFT JOIN __PREFIX__goods g
                ON gc.goods_id = g.id
                WHERE gc.category_id IN ($categoryId)
                AND g.is_on_sale = 1
                AND g.is_alone_sale = 1";
        $tmp = $this->query($sql);
        $data = array();
        if (!empty($tmp)) {
            foreach ($tmp as $val) {
                $data[] = $val['id'];
            }
        }
        return array_unique($data);
    }

    public function getGoodsIdByBrandId($brandId) {
        $sql = "SELECT g.id
                FROM __PREFIX__goods g
                WHERE g.brand_id = '{$brandId}'
                AND g.is_on_sale = 1
                AND g.is_alone_sale = 1";
        $tmp = $this->query($sql);
        $data = array();
        if (!empty($tmp)) {
            foreach ($tmp as $val) {
                $data[] = $val['id'];
            }
        }
        return $data;
    }

    /**
     * @function 检测加入购物车的商品是否正确
     * @author 良子
     * @param $goodsId
     * @param $number
     * @param string $productSn
     * @return int {1:商品不存,2:商品库存不足,3:正确}
     */
    public function checkAddToCart($goodsId, $number, $productSn = '') {
        if (empty($productSn)) {
            $sql = "SELECT g.number FROM __PREFIX__goods g
                    WHERE g.id='{$goodsId}'
                    AND g.is_on_sale=1
                    AND g.is_alone_sale=1";
        } else {
            $sql = "SELECT p.product_number number FROM __PREFIX__goods g
                    LEFT JOIN __PREFIX__products p
                    ON p.goods_id = g.id AND p.product_sn = '{$productSn}'
                    WHERE g.id='{$goodsId}'
                    AND g.is_on_sale=1
                    AND g.is_alone_sale=1";
        }
        $data = $this->query($sql);
        if (empty($data)) {
            return 1;
        }
        if ($data[0]['number'] < $number) {
            return 2;
        }
        return 3;
    }

    /**
     * @param $cartObj            购物车对象
     * @param array $selectGoods  购物车中选中的商品
     * @param bool $hasSelected   是否需要根据购物车中选中的商品，改变购物车的选中状态
     * @return array
     */
    public function getGoodsByCart($cartObj, $selectGoods = array(), $hasSelected = false) {
        $cart = $cartObj->getCart();
        $data = $this->getCartAndGiftGoods($cart, $selectGoods, $hasSelected);
        //更改购物车选中对象
        if (!empty($data)) {
            $updateCart = array();
            foreach($data as $key =>$val ) {
                $updateCart[$key]['goods_id'] = $val['id'];
                $updateCart[$key]['product_sn'] = $val['product_sn'];
                $updateCart[$key]['checked'] = $val['checked'];
            }
            $cartObj->updateChecked($updateCart);
        }

        return $data;
    }


    /**
     * @param $gift    赠品
     * @return array
     */
    public function getGiftGoods($gift) {
        $data = $this->getCartAndGiftGoods($gift);
        return $data;
    }

    private function getCartAndGiftGoods($goods, $selectGoods = array(), $hasSelected = false) {
        $data = array();
        $noProductSnCart = array(); //无可选属性产品
        $hasProductSnCart = array(); //有可选属性产品
        foreach ($goods as $val) {
            if (empty($val['product_sn'])) {
                $noProductSnCart[] = $val;
            } else {
                $hasProductSnCart[] = $val;
            }
        }
        $noProductSnSelect = array(); //无可选属性
        $hasProductSnSelect = array(); //有可选属性
        if (!empty($selectGoods)) {
            foreach ($selectGoods as $val) {
                if (empty($val['product_sn'])) {
                    $noProductSnSelect[] = $val['goods_id'];
                } else {
                    $hasProductSnSelect[] = $val['product_sn'];
                }
            }
        }

        if (!empty($noProductSnCart)) {
            $noProductSnGoods = array();
            foreach ($noProductSnCart as $val) {
                $noProductSnGoods[$val['goods_id']] = $val;
            }
            $noProductSnGoodsIds = implode(',', array_keys($noProductSnGoods));

            $query = $this->getCartGoodsByGoodsIds($noProductSnGoodsIds);
            foreach ($query as $key => $val) {
                if ($val['number'] < $noProductSnGoods[$val['id']]['number']) {
                    $val['has_number'] = 0;
                } else {
                    $val['has_number'] = 1;
                }
                $val['kucun'] = $val['number'];
                $val['number'] = $noProductSnGoods[$val['id']]['number'];
                $val['attrValue'] = '';
                $val['product_sn'] = '';
                if($val['has_number']==1 && $val['is_on_sale']==1) {
                    $val['disabled'] = 0;
                } else {
                    $val['disabled'] = 1;
                }
                if ($hasSelected) {
                    $val['checked'] = in_array($val['id'], $noProductSnSelect)?1:0;
                }  else {
                    $val['checked'] = $noProductSnGoods[$val['id']]['checked'];
                }

                $data[] = $val;
            }

        }

        if (!empty($hasProductSnCart)) {
            $hasProductSnGoods = array();
            foreach ($hasProductSnCart as $val) {
                $hasProductSnGoods[$val['product_sn']] = $val;
            }
            $hasProductSn = implode('\',\'', array_keys($hasProductSnGoods));

            $query = $this->getCartGoodsByProducts($hasProductSn);

            $GoodsAttr = D('GoodsAttr');
            foreach ($query as $key => $val) {
                if ($val['number'] < $hasProductSnGoods[$val['product_sn']]['number']) {
                    $val['has_number'] = 0;
                } else {
                    $val['has_number'] = 1;
                }
                $attrPrice = $GoodsAttr->getGoodsAttrPrice($val['id'], $val['attr_ids'],$val['attr_value']);
                $val['price'] += $attrPrice;
                $val['or_price'] = !empty($val['or_price'])?($val['or_price']+$attrPrice):0;
                $val['kucun'] = $val['number'];
                $val['number'] = $hasProductSnGoods[$val['product_sn']]['number'];
                if($val['has_number']==1 && $val['is_on_sale']==1) {
                    $val['disabled'] = 0;
                } else {
                    $val['disabled'] = 1;
                }
                if ($hasSelected) {
                    $val['checked'] = in_array($val['product_sn'], $hasProductSnSelect)?1:0;
                }  else {
                    $val['checked'] = $hasProductSnGoods[$val['product_sn']]['checked'];
                }
                $data[] = $val;
            }
        }
        return $data;
    }


    /**
     * @param $goodsIds
     * @return mixed
     */
    public function getCartGoodsByGoodsIds ($goodsIds) {
        if (empty($goodsIds))  {
            return array();
        }
        $sql = "SELECT g.id, g.name , g.thumb, g.number,g.is_on_sale,g.goods_sn,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),g.promote_price, g.price) price,
                IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),price, 0) or_price, g.integral, g.brand_id, g.suppliers_id, g.give_integral
                FROM __PREFIX__goods g
                WHERE g.id  in({$goodsIds})";
        $data = $this->query($sql);
        return $data;
    }

    /**
     * @param $products
     * @return mixed
     */
    public function getCartGoodsByProducts ($products) {
        if (empty($products)) {
            return array();
        }
        $sql = "SELECT g.id, g.name , g.thumb, p.product_number number,
                    g.is_on_sale, p.attr_value,p.attr_ids,p.product_sn,g.goods_sn,
                    IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),g.promote_price, g.price) price,
                    IF( (UNIX_TIMESTAMP()>=g.promote_start_date AND UNIX_TIMESTAMP()<=g.promote_end_date),price, 0) or_price, g.integral, g.brand_id, g.suppliers_id, g.give_integral
                FROM  __PREFIX__products p
                INNER JOIN __PREFIX__goods g
                ON p.goods_id = g.id
                WHERE p.product_sn  in('{$products}')";
        $data = $this->query($sql);
        return $data;
    }

    public function getCheckoutTotal($goodses, $type='cart', $error='') {
        $i = 0;
        //总计
        $total = 0;
        $totals = array();
        $totals[$i] = array('name'=>'商品总额', 'price'=>0, 'code'=>'goods_total');
        foreach($goodses as $goods) {
            $totals[$i]['price'] += ($goods['price']*$goods['number']);
        }
        $total += $totals[$i]['price'];
        //促销…
        if($total>0) {
            $Promotions = D('Promotions');
            $sales_promotion = $Promotions->getPromotionsTotal($goodses,$total);
            if ($sales_promotion>0) {
                $i++;
                $totals[$i] = array('name'=>'促销', 'price'=>0, 'code'=>'sales_promotion');
                $total = $total - $sales_promotion;
                $totals[$i]['price'] = $sales_promotion*(-1);
            }
        }

        if ($type=='checkout') {
            $shippingCode = session('checkout.shippingCode');
            //优惠券…
            $couponId = I("post.coupon");
            $Coupon = D('Coupon');
            $couponList = $Coupon->getAllCoupon($goodses, $total);
            cookie('checkout.couponList',serialize($couponList));
            if($total>0&&!empty($couponId)) {
                $coupon = $Coupon->getCouponTotal($couponId, $goodses, $total);
                if ($coupon>0) {
                    $i++;
                    $totals[$i] = array('name'=>'优惠券', 'price'=>0, 'code'=>'coupon');
                    $total = $total - $coupon;
                    $totals[$i]['price'] = $coupon*(-1);
                }
            }
            //积分…
            $payIntegral = I('post.pay_integral');
            $payIntegral = (int)$payIntegral;
            if (!empty($payIntegral) && $total>0) {
                if(!isset($error['pay_integral'])) {
                    $i++;
                    $totals[$i] = array('name'=>'积分', 'price'=>0, 'code'=>'integral');
                    if ( $payIntegral>$total*100 ) {
                        $totals[$i]['price'] = $total*(-1);
                        $total = 0;
                    } else {
                        $total = $total - round($payIntegral/100,2);
                        $totals[$i]['price'] = round($payIntegral/100,2)*(-1);
                    }
                }
            }
            //余额…
            $money = I('post.money');
            $money = (float)$money;
            if (!empty($money) && $total>0) {
                if(!isset($error['money'])) {
                    $i++;
                    $totals[$i] = array('name'=>'余额', 'price'=>0, 'code'=>'money');
                    if ( $money>$total ) {
                        $totals[$i]['price'] = $total*(-1);
                        $total = 0;
                    } else {
                        $total = $total - $money;
                        $totals[$i]['price'] = $money*(-1);
                    }
                }
            }

            //运费…
            if (!empty($shippingCode)) {
                $i++;
                $totals[$i] = array('name'=>'运费', 'price'=>0, 'code'=>'shipping');
                $totals[$i]['price'] = D('Shipping')->getShippingPrice($shippingCode, $total);
                $total += $totals[$i]['price'];
            }
        }
        array_push($totals, array('name'=>'支付总额','price'=>$total, 'code'=>'total'));
        return $totals;
    }



}

?>