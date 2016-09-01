<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AccountIntegralUsedModel extends Model {
    private $accountObj = '';

    public function _initialize () {
        $this->accountObj = \Org\Home\Account::getInstance();
    }

    public function getList($filter = array()) {
        $order = isset($filter['order']) ? $filter['order'] : 'ai.date_added DESC, ai.id DESC';
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

        $sql = "SELECT aiu.*
                FROM `__PREFIX__account_integral_used` aiu
                WHERE aiu.account_id ='{$this->accountObj->getAccountId()}' {$condition}
                ORDER By {$order}
                LIMIT {$start},{$limit}";
        $Data = $this->query($sql);

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
                FROM `__PREFIX__account_integral_used` aiu
                WHERE aiu.account_id ='{$this->accountObj->getAccountId()}' {$condition}";
        $data = $this->query($sql);
        return $data[0]['total'];
    }

}


?>