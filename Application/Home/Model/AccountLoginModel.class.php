<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AccountLoginModel extends Model {

    /**
     * @param $username
     * @return mix {false:没记录,int:错误次数}
     */
    public function getLoginTotal($username) {
        if (checkEmail($username)) {
            $where = " al.email = '{$username}' AND ";
        } else {
            $where = " al.username = '{$username}' AND ";
        }

        $sql = "SELECT al.error_total
                FROM __PREFIX__account_login al
                WHERE {$where} al.date_added = ".strtotime(date("Y-m-d"))."
                LIMIT 0,1";
        $data = $this->query($sql);

        if(!empty($data)) {
            $total = $data[0]['error_total'];
        } else {
            $total = false;
        }
        return $total;
    }

    public function addLoginTimes ($username) {
        $data = array();
        if (checkEmail($username)) {
            $data['email'] = $username;
        } else {
            $data['username'] = $username;
        }
        $error_total = $this->getLoginTotal($username);
        $data['ip'] = get_client_ip();
        $data['date_modified'] = time();
        if( $error_total === false ) {
            $data['error_total'] = 1;
            $data['date_added'] = strtotime(date("Y-m-d"));
            $result = $this->add($data);
        } else {
            $data['error_total'] = $error_total+1;
            $condition['username'] = $username;
            $condition['date_added'] = strtotime(date("Y-m-d"));
            $result = $this->where($condition)->save($data);
        }
        return $result;

    }

    public function resetLoginTimes($username) {
        $data['error_total'] = 0;
        $data['ip'] = get_client_ip();
        $data['date_modified'] = time();
        $condition['username'] = $username;
        $condition['date_added'] = strtotime(date("Y-m-d"));
        $result = $this->where($condition)->save($data);
        return $result;
    }

}


?>