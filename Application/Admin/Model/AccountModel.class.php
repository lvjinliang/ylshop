<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AccountModel extends CommonModel {

    public function getDataById($id) {
        $condition['id'] = $id;
        $data = $this->where($condition)->find();
        $AccountAddress = D('AccountAddress');
        $data['address'] = $AccountAddress->getAddressListByAccountId($data['id']);
        $AccountTransaction = D('AccountTransaction');
        $data['transaction'] = $AccountTransaction->getTransactionListByAccountId($data['id']);
        return $data;
    }


    public function getAccountDateTotal($filter = array()) {
        $sql = "SELECT count(*) total
                  FROM __PREFIX__tmp_date td
                  WHERE  td.setting_date>='{$filter['start_date']}'
                  AND td.setting_date<='{$filter['end_date']}'";
        $result = $this->query($sql);
        return $result[0]['total'];
    }

    public function getAccountDateList($filter = array(), $type='') {
        $start = isset($filter['start']) ? $filter['start'] : 0;
        $limit = isset($filter['limit']) ? $filter['limit'] : C('ADMIN_PAGE_SIZE');
        $limit = empty($limit) ? C('ADMIN_PAGE_SIZE') : $limit;
        $sql = "SELECT td.setting_date,
                    ( SELECT count(1) FROM __PREFIX__account
                      WHERE FROM_UNIXTIME(reg_time,'%Y-%m-%d')= td.setting_date) all_rows,
                    ( SELECT sum(1) FROM __PREFIX__account
                      WHERE FROM_UNIXTIME(reg_time,'%Y-%m-%d')= td.setting_date AND status=1) active_rows

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