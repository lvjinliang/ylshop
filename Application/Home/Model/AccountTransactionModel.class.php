<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AccountTransactionModel extends Model {
    private $accountObj = '';

    public function _initialize () {
        $this->accountObj = \Org\Home\Account::getInstance();
    }

    public function getList($filter = array()) {
        $order = isset($filter['order']) ? $filter['order'] : 'at.date_added DESC, at.id DESC';
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

        $sql = "SELECT at.*,o.order_no
                FROM `__PREFIX__account_transaction` at
                LEFT JOIN `__PREFIX__order` o
                ON at.order_id = o.id
                WHERE at.account_id ='{$this->accountObj->getAccountId()}'
                AND at.status=1 {$condition}
                ORDER By {$order}
                LIMIT {$start},{$limit}";
        $Data = $this->query($sql);
        echo $this->getDbError();
        return $Data;

    }

    public function getTotal($filter = array()) {
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
                FROM `__PREFIX__account_transaction` at
                WHERE at.account_id ='{$this->accountObj->getAccountId()}'
                AND at.status=1 {$condition}";
        $data = $this->query($sql);
        return $data[0]['total'];
    }


}


?>