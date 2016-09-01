<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class CouponModel extends CommonModel {
    protected $_auto = array(
        array('date_updated', 'time', 3, 'function'),
        array('date_added', 'time', 1, 'function'),
        array('start_time', 'changeTime', 3, 'callback'),
        array('end_time', 'changeTime', 3, 'callback')
    );

    public function changeTime() {
        $date = func_get_arg(0);
        if (!empty($date)) {
            $date = strtotime($date);
        } else {
            $date = '';
        }
        return $date;
    }

    public function addCoupon($data) {
        $coupon_id = $this->add();
        if ($coupon_id) {
            if(!empty($data['account_ids'])) {
                $CouponAccount = M('CouponAccount');
                $dataAccount= array();
                $account_ids = explode(',', $data['account_ids']);
                foreach($account_ids as $key=>$val) {
                    $dataAccount[$key]['coupon_id'] = $coupon_id;
                    $dataAccount[$key]['account_id'] = $val;
                }
                $CouponAccount->addAll($dataAccount);
            }
        }
        return $coupon_id;
    }

    public function saveCoupon($data) {
        $result = $this->save();

        if ($result !== false) {
            if(!empty($data['account_ids'])) {
                $CouponAccount = M('CouponAccount');
                $CouponAccount->where(array('coupon_id' => $data['id']))->delete();
                $dataAccount= array();
                $account_ids = explode(',', $data['account_ids']);
                foreach($account_ids as $key=>$val) {
                    $dataAccount[$key]['coupon_id'] = $data['id'];
                    $dataAccount[$key]['account_id'] = $val;
                }
                $CouponAccount->addAll($dataAccount);
            }
        }
        return $result;
    }



    public function getDataById($id) {
        $condition['id'] = $id;
        $data = $this->where($condition)->find();
        $data['start_time'] = date('Y-m-d', $data['start_time']);
        $data['end_time'] = date('Y-m-d', $data['end_time']);
        $CouponAccount = M('CouponAccount');
        $account_ids = $CouponAccount->field('account_id')->where(array('coupon_id'=>$id))->select();
        $data['account_ids'] = '';
        $tmp = array();
        if(count($account_ids)>0){
            foreach($account_ids as $key => $val) {
                $tmp[] = $val['account_id'];
            }
            $data['account_ids'] = implode(',', $tmp);
        }
        return $data;
    }

}


?>