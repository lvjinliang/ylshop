<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AccountAddressModel extends CommonModel {


    public function getAddressListByAccountId ($accountId) {
        $sql = "SELECT aa.*,a.address_id,country.name country_name,
                        province.name province_name,
                        city.name city_name, district.name district_name
                FROM __PREFIX__account_address aa
                LEFT JOIN __PREFIX__area country
                ON country.id = aa.country
                LEFT JOIN __PREFIX__account a
                ON aa.account_id=a.id
                LEFT JOIN __PREFIX__area province
                ON province.id = aa.province
                LEFT JOIN __PREFIX__area city
                ON city.id = aa.city
                LEFT JOIN __PREFIX__area district
                ON district.id = aa.district
                WHERE aa.account_id='{$accountId}'";
        $data = $this->query($sql);
        return $data;
    }



}


?>