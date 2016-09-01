<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Admin\Model;

use Think\Model;

class AdminUserActivityModel extends Model {

    public function addActivity ($id, $key) {
        $dataActivity['user_id'] = $id;
        $dataActivity['key'] = $key;
        $dataActivity['ip'] = get_client_ip();
        $dataActivity['date_added'] = time();
        $this->add($dataActivity);
    }

}


?>