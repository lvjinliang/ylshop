<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AccountAddressModel extends Model {


    public function getDataById($id) {
        $sql = "SELECT aa.*,country.name country_name,province.name province_name,
                        city.name city_name, district.name district_name
                FROM __PREFIX__account_address aa
                LEFT JOIN __PREFIX__area country
                ON country.id = aa.country
                LEFT JOIN __PREFIX__area province
                ON province.id = aa.province
                LEFT JOIN __PREFIX__area city
                ON city.id = aa.city
                LEFT JOIN __PREFIX__area district
                ON district.id = aa.district
                WHERE aa.id='{$id}'
                LIMIT 0,1";
        $data = $this->query($sql);
        return empty($data)?array():$data[0];
    }

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

    public function setDefault($accountId, $addressId) {
        $sql = "UPDATE __PREFIX__account
                SET  address_id = '{$addressId}'
                WHERE id='{$accountId}'";
        return $this->execute($sql);
    }
}


?>