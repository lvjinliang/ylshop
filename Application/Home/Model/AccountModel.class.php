<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class AccountModel extends Model {
    protected $_auto = array(
        array('reg_time', 'time', 1, 'function'),
        array('last_time', 'time', 3, 'function')
    );

    /*
     * @function 检测字段唯一性
     * @param string $field 字段名
     * @param string $value 字段值
     * @param array  $param 过滤字段 array('fieldName'=>'value')
     * @return boolen {false:'不唯一',true:'唯一'}
     *
     */
    public function checkUnique($field, $value, $param = array()) {
        if(!empty($param)){
            foreach($param as $key=>$val) {
                if(!empty($val)){
                    $condition[$key] = $val;
                }
            }
        }
        $condition[$field] = $value;
        $result = $this->where($condition)->count($field);
        return empty($result)?true:false;
    }

    public function register ($data) {
        $this->salt = \Org\Util\String::randString();
        $this->password = md5(md5($this->password).$this->salt);
        $this->reg_ip = $this->last_ip = $ip = get_client_ip();
        $this->login_times = 1;
        $this->status = 1;
        //是否需要邮箱验证
        if( C('MAIL_REGISTER_VERIFY') ) {
            $this->is_validated = 0;
        } else {
            $this->is_validated = 1;
        }
        $accountId = $this->add();
        if( $accountId ) {
            //用户活动
            $AccountActivity = D('AccountActivity');
            $AccountActivity->addActivity($accountId, 'register');
            if( C('MAIL_REGISTER_VERIFY') ) {
                //邮件验证code
                $dataEmailVerify['email'] = $data['email'];
                $dataEmailVerify['account_id'] = $accountId;
                $dataEmailVerify['type'] = 'register';
                $dataEmailVerify['code'] = \Org\Util\String::keyGen();
                $dataEmailVerify['date_added'] = time();
                $dataEmailVerify['date_end'] = time()+24*3600;
                $dataEmailVerify['status'] = 1;
                $EmailVerify = D('EmailVerify');
                $status = $EmailVerify->add($dataEmailVerify);
                //添加邮件记录
                if( $status ) {
                    $code = urlencode(base64_encode(\Think\Crypt::encrypt($dataEmailVerify['code'],C('ENCRYPT_KEY'))));
                    \Org\Util\Email::AddRegisterEmailVerify($data['email'],$data['username'],$code);
                }
            }

        }

        return $accountId;

    }


    /**
     * @param $username
     * @param $password
     * @return int {1:用户名为空,2:密码为空,3:该用户名末注册或末激活,4:密码错误,99:登录成功}
     */
    public function checkLogin($username, $password) {
        //可用用户名或邮箱登录
        if(empty($username)) {
            return 1;
        }
        if(empty($password)) {
            return 2;
        }
        $Account = \Org\Home\Account::getInstance();
        $result = $Account->login($username, $password);
        return $result;
    }

    public function updateAccount ($data) {

    }



}


?>