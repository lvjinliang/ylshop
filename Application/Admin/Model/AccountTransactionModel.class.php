<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AccountTransactionModel extends CommonModel {


    public function getTransactionListByAccountId ($accountId) {
        $data = $this->where('account_id='.$accountId)->select();
        return $data;
    }



}


?>