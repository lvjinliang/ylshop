<?php
namespace Org\Home;
class Account {
    public $accountInfo = '';
    private static $s = array();
    private function __construct() {
        if ($this->isLogin()) {
            $sql = "SELECT *
                    FROM __PREFIX__account a
                    WHERE a.status=1
                    AND a.is_validated=1
                    AND id='" . session('account.id') . "'";
            $accountInfo = M('Account')->query($sql);
            unset($accountInfo[0]['password']);
            unset($accountInfo[0]['salt']);
            if (!empty($accountInfo)) {
                $this->accountInfo = $accountInfo[0];
            }
            unset($accountInfo);
        }
    }

    public static function getInstance(){
        if(empty(self::$s['account'])){
            self::$s['account'] = new Account();
        }
        return self::$s['account'];
    }

    public function getAccountId() {
        return isset($this->accountInfo['id'])?$this->accountInfo['id']:'';
    }

    public function getAccountUsername() {
        return isset($this->accountInfo['username'])?$this->accountInfo['name']:'';
    }

    public function getAccountRank() {
        if ($this->isLogin()) {
            $sql = "SELECT id
                    FROM __PREFIX__account_rank ar
                    WHERE ar.min_points<='{$this->accountInfo['rank_integral']}'
                    AND ar.max_points>='{$this->accountInfo['rank_integral']}'
                    ORDER BY ar.sort DESC , ar.id DESC
                    LIMIT 0,1 ";
            $data = M('AccountRank')->query($sql);
            return empty($data) ? 0 : $data[0]['id'];
        } else {
            return 0;
        }
    }

    public function isLogin() {
        if (session('?account.id')) {
            return true;
        } else {
            return false;
        }
    }

    public function login($username, $password, $override = false) {
        $where = '';
        if (checkEmail($username)) {
            $where = " AND a.email = '{$username}'";
        } else {
            $where = " AND a.username = '{$username}'";
        }
        $sql = "SELECT *
                FROM __PREFIX__account a
                WHERE a.status=1 AND a.is_validated=1 {$where}";
        $accountInfo = M('Account')->query($sql);
        if (empty($accountInfo)) {
            return 3;
        }
        //需要密码验证
        if (!$override) {
            if (md5(md5($password) . $accountInfo[0]['salt']) !== $accountInfo[0]['password']) {
                return 4;
            }
        }

        $this->accountInfo = $accountInfo[0];
        //用户活动
        $AccountActivity = D('AccountActivity');
        $AccountActivity->addActivity($this->accountInfo['id'], 'login');

        $accountData = array();
        $accountData['last_time'] = time();
        $accountData['last_ip'] = get_client_ip();
        $accountData['login_times'] = $this->accountInfo['login_times']+1;
        M('Account')->where('id='.$this->accountInfo['id'])->data($accountData)->save();

        $Cart = New \Org\Home\Cart($this);
        session('account.id', $this->accountInfo['id']);
        $Cart->mergeCartAndDb();
        return 99;
    }

    public function logout() {
        //用户活动
        if($this->isLogin()){
            $AccountActivity = D('AccountActivity');
            $AccountActivity->addActivity($this->accountInfo['id'], 'logout');
        }
        session('account.id', null);
        \Org\Home\Cart::emptyCart();
    }

    public function updateIntegral($integral) {
        if($this->isLogin()) {
            $condition['account_id'] = $this->getAccountId();
            $condition['status'] = 1;
            $condition['start_date'] = array('elt', date('Y-m-d'));
            $condition['end_date'] = array('egt', date('Y-m-d'));
            $data = M('AccountIntegral')->where($condition)->order(array('date_added'=>'ASC'))->select();
            foreach ($data as $key => $val ) {
                if (($val['points']-$val['used'])>=$integral) {
                    M('AccountIntegral')->where(array('id'=>$val['id']))->setInc('used',$integral);
                    break;
                } else if(($val['points']-$val['used']) <= 0){
                    continue;
                } else {
                    M('AccountIntegral')->where(array('id'=>$val['id']))->setInc('used',($val['points']-$val['used']));
                    $integral = $integral-($val['points']-$val['used']);
                }
            }

        } else {
            return false;
        }
        return ture;
    }

    public function  getIntergral() {
        $integral = 0;
        if($this->isLogin()) {
            $condition['account_id'] = $this->getAccountId();
            $condition['status'] = 1;
            $condition['start_date'] = array('elt', date('Y-m-d'));
            $condition['end_date'] = array('egt', date('Y-m-d'));
            $integral = M('AccountIntegral')->field('(sum(points)-sum(used)) total')->where($condition)->find();
            $integral = $integral['total'];
        }

        return $integral;
    }

    public function addInergral () {

    }


    public function updateMoney($money) {
        $result = false;
        if($this->isLogin()) {
            $result = M('Account')->where(array('id'=>$this->getAccountId()))->setInc('money', $money);
            return $result;
        }
        return $result;
    }



}