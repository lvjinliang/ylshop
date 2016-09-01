<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class BrandModel extends Model {

    public function getBrand() {
        $sql = "SELECT * FROM __PREFIX__brand
                WHERE status = 1
                ORDER BY sort DESC, id DESC";
        $data = $this->query($sql);
        return $data;
    }


}


?>