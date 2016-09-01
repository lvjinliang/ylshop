<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class ReviewModel extends Model {
    private $accountObj = '';

    public function _initialize () {
        $this->accountObj = \Org\Home\Account::getInstance();
    }

    public function getNotReviewList($filter = array()) {
        $order = isset($filter['order']) ? $filter['order'] : 'o.date_added DESC, o.id DESC';
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('PAGE_SIZE');

        $sql = "SELECT o.id,o.order_no,o.date_added
                FROM `__PREFIX__order` o
                INNER JOIN `__PREFIX__order_goods` og
                ON o.id = og.order_id
                WHERE o.order_status_id =6
                AND o.account_id = '".$this->accountObj->getAccountId()."'
                AND NOT EXISTS (
                  SELECT 1 FROM `__PREFIX__review` r
                  WHERE r.order_no = o.order_no
                  AND r.goods_id = og.goods_id
                  AND r.product_sn = og.product_sn
                )
                GROUP BY o.id
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
                    INNER JOIN __PREFIX__order o
                    ON o.id = og.order_id
                    WHERE og.order_id in($orderIds)
                    AND NOT EXISTS (
                      SELECT 1 FROM `__PREFIX__review` r
                      WHERE r.order_no = o.order_no
                      AND r.goods_id = og.goods_id
                      AND r.product_sn = og.product_sn
                    )
                    ORDER BY og.order_id DESC, og.id DESC";
            $goodsData = $this->query($sql);
            foreach ($orderData as $key => $val) {
                foreach ($goodsData as $v) {
                    if ($val['id'] == $v['order_id']) {
                        $orderData[$key]['goods'][] = $v;
                    }
                }
            }
        }

        return $orderData;
    }

    public function getNotReviewTotal() {
        $sql = "SELECT count(*) total
                FROM `__PREFIX__order` o
                INNER JOIN `__PREFIX__order_goods` og
                ON o.id = og.order_id
                WHERE o.order_status_id =6
                AND o.account_id = '".$this->accountObj->getAccountId()."'
                AND NOT EXISTS (
                   SELECT 1 FROM `__PREFIX__review` r
                   WHERE r.order_no = o.order_no
                   AND r.goods_id = og.goods_id
                   AND r.product_sn = og.product_sn
                )";
        $data = $this->query($sql);
        return $data[0]['total'];
    }

    public function getReviewList ($filter = array()) {
        $order = isset($filter['order']) ? $filter['order'] : 'r.date_added DESC, r.id DESC';
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

        $sql = "SELECT o.date_added order_date, og.price,og.number,
                       og.name, og.or_price,g.thumb ,r.*
                FROM `__PREFIX__review` r
                INNER JOIN __PREFIX__order o
                ON r.order_no = o.order_no
                INNER JOIN __PREFIX__order_goods og
                ON og.order_id = o.id
                INNER JOIN __PREFIX__goods g
                ON r.goods_id = g.id
                WHERE r.account_id='".$this->accountObj->getAccountId()."' {$condition}
                ORDER By {$order}
                LIMIT {$start},{$limit}";
        $reviewData = $this->query($sql);
        return $reviewData;
    }

    public function getReviewTotal($filter = array()) {
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
                FROM `__PREFIX__review` r
                INNER JOIN __PREFIX__order o
                ON r.order_no = o.order_no
                INNER JOIN __PREFIX__order_goods og
                ON og.order_id = o.id
                INNER JOIN __PREFIX__goods g
                ON r.goods_id = g.id
                WHERE r.account_id='".$this->accountObj->getAccountId()."' {$condition}";
        $data = $this->query($sql);
        return $data[0]['total'];
    }

    public function checkIsMyBuy($order_no, $goods_id, $product_sn='') {
        if (!empty($product_sn)) {
            $product_sn = " AND og.product_sn='{$product_sn}' ";
        }
        $sql = "SELECT count(*) total
                FROM __PREFIX__order_goods og
                INNER JOIN __PREFIX__order o
                ON og.order_id = o.id
                WHERE o.account_id = '".$this->accountObj->getAccountId()."'
                AND o.order_no = '{$order_no}'
                AND og.goods_id='{$goods_id}' {$product_sn} ";
        $data = $this->query($sql);
        return empty($data[0]['total']) ? false : true;
    }

    public function checkHadReview ($order_no, $goods_id, $product_sn='') {
        if (!empty($product_sn)) {
            $product_sn = " AND product_sn='{$product_sn}' ";
        }
        $sql = "SELECT count(*) total
                FROM __PREFIX__review
                WHERE account_id = '".$this->accountObj->getAccountId()."'
                AND order_no = '{$order_no}'
                AND goods_id='{$goods_id}' {$product_sn} ";
        $data = $this->query($sql);
        return empty($data[0]['total']) ? false : true;
    }

    public function addReview($data) {
        if (!empty($data['product_sn'])) {
            $data['goods_attr'] = $this->getGoodsAttrByProductSn($data['order_no'], $data['product_sn']);
        }
        $data['account_id'] = $this->accountObj->getAccountId();
        $data['status'] = 0;
        $data['date_added'] = time();
        $data['date_updated'] = time();
        $result = $this->add($data);
        return $result;
    }

    public function getGoodsAttrByProductSn ($order_no, $product_sn) {
        $sql = "SELECT og.goods_attr
                FROM __PREFIX__order_goods og
                INNER JOIN __PREFIX__order o
                ON og.order_id = o.id
                WHERE o.order_no='{$order_no}'
                AND og.product_sn='{$product_sn}'";
        $data = $this->query($sql);
        return empty($data) ? '' : $data[0]['goods_attr'];
    }


    public function getReviewByGoodsId($goods_id, $filter=array()) {
        $order = isset($filter['order']) ? $filter['order'] : 'r.is_top DESC, r.id DESC';
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

        $sql = "SELECT o.date_added order_date, og.price,og.number,
                       og.name, og.or_price,g.thumb ,r.*,a.username account_name
                FROM `__PREFIX__review` r
                INNER JOIN __PREFIX__order o
                ON r.order_no = o.order_no
                INNER JOIN __PREFIX__order_goods og
                ON og.order_id = o.id
                INNER JOIN __PREFIX__goods g
                ON r.goods_id = g.id
                INNER JOIN __PREFIX__account a
                ON r.account_id = a.id
                WHERE r.goods_id='{$goods_id}'
                AND r.status=1 {$condition}
                ORDER By {$order}
                LIMIT {$start},{$limit}";
        $reviewData = $this->query($sql);
        return $reviewData;
    }

    public function getTotalReviewByGoodsId($goods_id, $filter=array()) {
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
                FROM `__PREFIX__review` r
                INNER JOIN __PREFIX__order o
                ON r.order_no = o.order_no
                INNER JOIN __PREFIX__order_goods og
                ON og.order_id = o.id
                INNER JOIN __PREFIX__goods g
                ON r.goods_id = g.id
                INNER JOIN __PREFIX__account a
                ON r.account_id = a.id
                WHERE r.goods_id='{$goods_id}'
                AND r.status=1 {$condition} ";
        $data = $this->query($sql);
        return $data[0]['total'];
    }





}


?>