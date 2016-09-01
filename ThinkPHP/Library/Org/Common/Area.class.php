<?php
namespace Org\Common;
class Area {
    private $Area = '';

    public function __construct() {
        $this->Area = M('Area');
    }

    public function getProvinces() {
        $sql = "SELECT id,`name`
                FROM __PREFIX__area
                WHERE type=1 AND status=1
                ORDER BY sort desc, id desc";
        $data = $this->Area->query($sql);
        return $data;
    }

    public function getCityByProvinceId($provinceId) {
        $sql = "SELECT id,`name`
                FROM __PREFIX__area
                WHERE type=2 AND status=1 AND pid='{$provinceId}'
                ORDER BY sort desc, id desc";
        $data = $this->Area->query($sql);
        return $data;
    }

    public function getDistrictByCityId($cityId) {
        $sql = "SELECT id,`name`
                FROM __PREFIX__area
                WHERE type=3 AND status=1 AND pid='{$cityId}'
                ORDER BY sort desc, id desc";
        $data = $this->Area->query($sql);
        return $data;
    }

    public function getNameById($id) {
        $data = $this->Area->field('name')->where(array('id'=>$id))->find();
        return empty($data)?'':$data['name'];
    }


}