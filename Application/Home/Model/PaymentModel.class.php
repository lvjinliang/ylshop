<?php
/**
 * User: 良子
 * Date: 16-03-30
 */

namespace Home\Model;

use Think\Model;

class PaymentModel extends Model {

     public function getList () {
         $sql = "SELECT * FROM __PREFIX__payment p
                 WHERE p.status=1
                 ORDER BY p.sort DESC,id DESC ";
         $data = $this->query($sql);
         return $data;
     }

    public function checkPaymentCode($code) {
        $sql = "SELECT count(*) total FROM __PREFIX__payment p
                WHERE p.code = '{$code}'
                LIMIT 0,1";
        $data = $this->query($sql);
        if($data[0]['total']>0) {
            return true;
        } else {
            return false;
        }
    }

    public function getPaymentMethodByCode($code) {
        $sql = "SELECT `name` FROM __PREFIX__payment p
                WHERE p.code = '{$code}'
                LIMIT 0,1";
        $data = $this->query($sql);
        return empty($data) ? '':$data[0]['name'];
    }

    public function getPaymentByCode($code) {
        $sql = "SELECT * FROM __PREFIX__payment p
                WHERE p.code = '{$code}'
                LIMIT 0,1";
        $data = $this->query($sql);
        return empty($data) ? '':$data[0];
    }


}


?>