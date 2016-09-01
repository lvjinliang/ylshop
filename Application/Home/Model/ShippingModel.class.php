<?php
/**
 * User: 良子
 * Date: 16-03-30
 */

namespace Home\Model;

use Think\Model;

class ShippingModel extends Model {

    public function getList() {
        $sql = "SELECT * FROM __PREFIX__shipping s
                WHERE s.status=1
                ORDER BY s.sort DESC,id DESC ";
        $data = $this->query($sql);
        return $data;
    }

    public function getShippingPrice($code, $total) {
        $price = 0;
        $data = $this->getShippingByCode($code);
        if (!empty($data)) {
            //只有数字
            if (preg_match('/^\d*$/',$data[0]['shipping_rate'])) {
                $price = empty($data[0]['shipping_rate'])?0:$data[0]['shipping_rate'];
            } else {
                $shippingRate = explode(',', $data[0]['shipping_rate']);
                foreach($shippingRate as $key => $val) {
                    $price_qj = explode(':', $val);
                    $qj = explode('-', $price_qj[0]);
                    if (empty($qj[1])) {
                        if ($total>=$qj[0]) {
                            $price = $price_qj[1];
                            break;
                        }
                    } else {
                        if ($total>=$qj[0] && $total<$qj[1]) {
                            $price = $price_qj[1];
                            break;
                        }
                    }

                }
            }
        }
        return $price;
    }

    public function getShippingByCode($code) {
        $sql = "SELECT * FROM __PREFIX__shipping s
                WHERE s.code = '{$code}'
                ORDER BY s.sort DESC,id DESC
                LIMIT 0,1";
        $data = $this->query($sql);
        return $data;
    }

    public function checkShippingCode($code) {
        $sql = "SELECT count(*) total FROM __PREFIX__shipping s
                WHERE s.code = '{$code}'
                LIMIT 0,1";
        $data = $this->query($sql);
        if($data[0]['total']>0) {
            return true;
        } else {
            return false;
        }
    }

}


?>