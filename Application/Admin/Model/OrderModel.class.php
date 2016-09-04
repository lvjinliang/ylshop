<?php
/**
 * User: 良子
 * Date: 16-04-14
 */

namespace Admin\Model;

use Think\Model;

class OrderModel extends CommonModel {

    public function getAllOrderStatus() {
          return M('OrderStatus')->select();
    }

    public function getAllPaymentStatus() {
        return M('PaymentStatus')->select();
    }

    public function getLists($filter = array()) {
        //设置分页
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('ADMIN_PAGE_SIZE');
        $limit = empty($limit) ? C('ADMIN_PAGE_SIZE') : $limit;

        //设置排序
        $order = isset($filter['order']) ? $filter['order'] : array();
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);

        $result = $this->field('o.*, a.username,os.name order_status,ps.`name` payment_status')
                ->alias('o')
                ->join('LEFT JOIN __ACCOUNT__ a ON a.id=o.account_id')
                ->join('LEFT JOIN __ORDER_STATUS__ os ON o.order_status_id=os.order_status_id')
                ->join('LEFT JOIN __PAYMENT_STATUS__ ps ON o.payment_status_id=ps.payment_status_id')
                ->where($filter)
                ->order($order)
                ->limit($start, $limit)
                ->select();
        return $result;
    }

    public function getTotal($filter = array()) {
        unset($filter['start']);
        unset($filter['limit']);
        unset($filter['order']);
        $filter = $this->filter($filter);
        $result = $this->alias('o')
            ->join('LEFT JOIN __ACCOUNT__ a ON a.id=o.account_id')
            ->join('LEFT JOIN __ORDER_STATUS__ os ON o.order_status_id=os.order_status_id')
            ->join('LEFT JOIN __PAYMENT_STATUS__ ps ON o.payment_status_id=ps.payment_status_id')
            ->where($filter)->count();
        return $result;
    }

    public function getDataById($id) {
        $OrderObj = new \Org\Common\Order ();
        $data = $OrderObj->getDataById($id);
        return $data;
    }

    public function getOrderDateTotal($filter = array()) {
        $sql = "SELECT count(*) total
                  FROM __PREFIX__tmp_date td
                  WHERE  td.setting_date>='{$filter['start_date']}'
                  AND td.setting_date<='{$filter['end_date']}'";
        $result = $this->query($sql);
        return $result[0]['total'];
    }

    public function getOrderDateList($filter = array(), $type='') {

        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('ADMIN_PAGE_SIZE');
        $limit = empty($limit) ? C('ADMIN_PAGE_SIZE') : $limit;
        $sql = "SELECT td.setting_date,
                    IFNULL( (SELECT count(1) FROM __PREFIX__order
                      WHERE FROM_UNIXTIME(date_added,'%Y-%m-%d')= td.setting_date), 0) all_rows,
                    IFNULL( (SELECT sum(total) FROM __PREFIX__order
                      WHERE FROM_UNIXTIME(date_added,'%Y-%m-%d')= td.setting_date), 0) all_total,
                    IFNULL( (SELECT count(1) FROM __PREFIX__order
                      WHERE FROM_UNIXTIME(date_added,'%Y-%m-%d')= td.setting_date AND payment_status_id=1), 0) pay_rows,
                    IFNULL( (SELECT sum(total) FROM __PREFIX__order
                      WHERE FROM_UNIXTIME(date_added,'%Y-%m-%d')= td.setting_date AND payment_status_id=1), 0) pay_total,
                    IFNULL( (SELECT count(1) FROM __PREFIX__order
                     WHERE FROM_UNIXTIME(date_added,'%Y-%m-%d')= td.setting_date AND order_status_id in(7,9)), 0) back_rows
                  FROM __PREFIX__tmp_date td
                  WHERE  td.setting_date>='{$filter['start_date']}'
                  AND td.setting_date<='{$filter['end_date']}'
                  ORDER BY td.setting_date ASC ";
        if (empty($type)) {
            $sql .= "LIMIT {$start}, {$limit}";
        }

        $result = $this->query($sql);
        return $result;
    }

}


?>