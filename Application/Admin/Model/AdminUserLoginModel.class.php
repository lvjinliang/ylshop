<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Admin\Model;

use Think\Model;

class AdminUserLoginModel extends Model {

    /**
     * @param $username
     * @return mix {false:没记录,int:错误次数}
     */
    public function getLoginTotal($name) {
        $sql = "SELECT aul.error_total
                FROM __PREFIX__admin_user_login aul
                WHERE aul.name='{$name}' AND aul.date_added = ".strtotime(date("Y-m-d"))."
                LIMIT 0,1";
        $data = $this->query($sql);

        if(!empty($data)) {
            $total = $data[0]['error_total'];
        } else {
            $total = false;
        }
        return $total;
    }

    public function addLoginTimes ($name) {
        $data = array();
        $data['name'] = $name;
        $error_total = $this->getLoginTotal($name);
        $data['ip'] = get_client_ip();
        $data['date_modified'] = time();
        if( $error_total === false ) {
            $data['error_total'] = 1;
            $data['date_added'] = strtotime(date("Y-m-d"));
            $result = $this->add($data);
        } else {
            $data['error_total'] = $error_total+1;
            $condition['name'] = $name;
            $condition['date_added'] = strtotime(date("Y-m-d"));
            $result = $this->where($condition)->save($data);
        }

        return $result;

    }

    public function resetLoginTimes($name) {
        $data['error_total'] = 0;
        $data['ip'] = get_client_ip();
        $data['date_modified'] = time();
        $condition['name'] = $name;
        $condition['date_added'] = strtotime(date("Y-m-d"));
        $result = $this->where($condition)->save($data);
        return $result;
    }

}


?>