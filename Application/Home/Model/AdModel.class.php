<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AdModel extends Model {


    public function getDataByCode($code) {
        $sql = "SELECT ai.* FROM __PREFIX__ad a
                LEFT JOIN __PREFIX__ad_image ai
                ON a.id = ai.ad_id
                WHERE a.status=1 AND code='{$code}'
                ORDER BY ai.sort DESC";
        $data = $this->query($sql);
        return $data;
    }


}


?>