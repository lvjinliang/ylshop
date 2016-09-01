<?php
/**
 * User: 良子
 * Date: 15-12-9
 */

namespace Admin\Model;

use Think\Model;

class AdminUserModel extends CommonModel {
    protected $_auto = array (
        array('date_updated','time',3,'function'),
        array('date_added','time',1,'function')
    );


    public function getDateTime(){
        return date('Y-m-d H:i:s', time());
    }

    public function getSalt(){
        $salt = \Org\Util\String::randString();
        return $salt;
    }

    public function getPassword($password, $salt) {
        return md5(md5($password).$salt);
    }

    /**
     * @param $user
     * @param $password
     * @return int {1:用户名为空,2:密码为空,3:该用户名末注册或末激活,4:密码错误,99:登录成功}
     */
    public function checkLogin($name, $password) {

        if(empty($name)) {
            return 1;
        }
        if(empty($password)) {
            return 2;
        }

        $where['name'] = $name;
        $where['status'] = 1;

        $userInfo = $this->where($where)->find();
        if (empty($userInfo)) {
            return 3;
        }

        if (md5(md5($password) . $userInfo['salt']) !== $userInfo['password']) {
            return 4;
        }
        session('user.id',$userInfo['id']);
        session('user.name',$userInfo['name']);
        //用户活动
        $AdminUserActivity = D('AdminUserActivity');
        $AdminUserActivity->addActivity($userInfo['id'], 'login');
        return 99;
        return $result;
    }

}


?>