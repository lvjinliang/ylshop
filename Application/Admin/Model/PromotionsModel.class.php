<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class PromotionsModel extends CommonModel {
    protected $_auto = array(
        array('date_updated', 'time', 3, 'function'),
        array('date_added', 'time', 1, 'function'),
        array('start_time', 'changeTime', 3, 'callback'),
        array('end_time', 'changeTime', 3, 'callback'),
        array('account_rank', 'changeAccountRank', 3, 'callback'),
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

    public function changeAccountRank(){
        $AccountRanks = func_get_arg(0);
        if (!empty($AccountRanks)) {
            $AccountRanks = implode(',',$AccountRanks);
        } else {
            $AccountRanks = '';
        }
        return $AccountRanks;
    }

    public function getDataById($id) {
        $condition['id'] = $id;
        $data = $this->where($condition)->find();
        $data['start_time'] = date('Y-m-d', $data['start_time']);
        $data['end_time'] = date('Y-m-d', $data['end_time']);
        $data['account_rank'] = explode(',',$data['account_rank']);
        return $data;
    }

}


?>