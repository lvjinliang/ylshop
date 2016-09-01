<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AreaModel extends Model {
    private $AreaObj = '';
    public function _initialize () {
        $this->AreaObj = new \Org\Common\Area ();
    }

    public function getProvinces() {
        $data = $this->AreaObj->getProvinces();
        return $data;
    }

    public function getCityByProvinceId($provinceId) {
        $data = $this->AreaObj->getCityByProvinceId($provinceId);
        return $data;
    }

    public function getDistrictByCityId($cityId) {
        $data = $this->AreaObj->getDistrictByCityId($cityId);
        return $data;
    }

}


?>