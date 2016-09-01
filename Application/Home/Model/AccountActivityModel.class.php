<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AccountActivityModel extends Model {

    public function addActivity ($id, $type) {
        $dataActivity['account_id'] = $id;
        $dataActivity['key'] = $type;
        $dataActivity['ip'] = get_client_ip();
        $dataActivity['date_added'] = time();
        return $this->add($dataActivity);
    }

}


?>