<?php
namespace Home\Controller;
use Think\Controller;

class AddressController extends CommonController {
    private $AccountAddress = '';
    private $error = '';

    public function _initialize() {
        parent::_initialize();
        $this->AccountAddress = D('AccountAddress');
        $this->error = array();
    }

    public function index() {
        $this->display();
    }

    public function add() {
        if (!$this->accountObj->getAccountId()) {
            echo '请先登录！<script>window.location.href="'.U('Home/login/index').'"</script>';
            exit();
        }
        $this->assign('dataUrl',U('Home/address/add'));
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->AccountAddress->create();
            $this->AccountAddress->account_id = $this->accountObj->getAccountId();
            $this->AccountAddress->country = 1;
            if ($this->AccountAddress->add()) {
                echo 1;
                exit();
            } else {
                $this->assign('errorInfo', $this->AccountAddress->getDbError());
                $this->form();
            }
        } else {
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function update() {
        if (!$this->accountObj->getAccountId()) {
            echo '请先登录！<script>window.location.href="'.U('Home/login/index').'"</script>';
            exit();
        }
        $this->assign('dataUrl',U('Home/address/update'));
        if (($_SERVER['REQUEST_METHOD'] == 'POST') && $this->validate_form()) {
            $this->AccountAddress->create();
            $this->AccountAddress->account_id = $this->accountObj->getAccountId();
            if ($this->AccountAddress->save()!==false) {
                echo 1;
                exit();
            } else {
                $this->assign('errorInfo', $this->AccountAddress->getDbError());
                $this->form();
            }
        } else {
            $id = I('get.id');
            if (empty($id)) {
                echo '非法操作！<script>window.location.href="'.U('Home/index/index').'"</script>';
                exit();
            }
            $this->assign('error', $this->error);
            $this->form();
        }
    }

    public function validate_form() {
        if (!checkRequire(I('post.name'))) {
            $this->error['name'] = '收货人不能为空';
        } else {
            $nameLen = mb_strlen(I('post.name'),'UTF8');
            if ( $nameLen<2 && $nameLen<= 30 ) {
                $this->error['name'] = '收货人名字长度在2到30个字符';
            }
        }

        if (!checkTel(I('post.telephone'))) {
            $this->error['telephone'] = '请输入正确的手机号码';
        }

        if (!checkRequire(I('post.province'))) {
            $this->error['area'] = '请选择省';
        } else if (!checkRequire(I('post.city'))) {
            $this->error['area'] = '请选择市';
        } else if (!checkRequire(I('post.district'))) {
            $this->error['area'] = '请选择区县';
        }
        $addressLen = mb_strlen(I('post.address'),'UTF8');
        if ( $addressLen<6 && $nameLen<= 50 ) {
            $this->error['address'] = '收货地址长度在6到50个字符';
        }

        return (empty($this->error)) ? true : false;
    }

    public function delete() {
        $json = array('success'=>1,'msg'=>'');
        if (!$this->accountObj->getAccountId()) {
            $json['success'] = 0;
            $json['msg'] = '请先登录！<script>window.location.href="'.U('Home/login/index').'"</script>';
            $this->ajaxReturn($json);
            exit();
        }

        $id = I('get.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法操作';
        } else {
            if($this->AccountAddress->where(array('id'=>$id))->delete()!==false){
                $json['success'] = 1;
                $json['msg'] = '删除成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = $this->AccountAddress->getDbError();
            }
        }
        $this->ajaxReturn($json);
    }

    public function form() {
        layout(false);
        $data['id'] = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = I('post.');
        } else {
            $id = I('get.id');
            if (!empty($id)) {
                $data = $this->AccountAddress->getDataById($id);
            } else {
                $data['name'] = '';
                $data['telephone'] = '';
                $data['country'] = '1';
                $data['province'] = '';
                $data['city'] = '';
                $data['district'] = '';
                $data['address'] = '';
                $data['zipcode'] = '';
            }
        }
        $Area = D('Area');
        $data['provinces'] = $Area->getProvinces();
        $data['citys'] = array();
        if (!empty($data['province'])) {
            $data['citys'] = $Area->getCityByProvinceId($data['province']);
        }
        $data['districts'] = array();
        if (!empty($data['city'])) {
            $data['districts'] = $Area->getDistrictByCityId($data['city']);
        }
        $this->assign('data', $data);
        $this->display('form');
    }

    public function ajax_get_city() {
        layout(false);
        $provinceId = I('post.id');
        $content = '<option value="" class="default">--请选择市--</option>';
        if (empty($provinceId)) {
            echo $content;
            exit();
        }
        $Area = D('Area');
        $citys = $Area->getCityByProvinceId($provinceId);
        foreach($citys as $val) {
            $content .='<option value="'.$val['id'].'">'.$val['name'].'</option>';
        }
        echo $content;
        exit();
    }

    public function ajax_get_district() {
        layout(false);
        $cityId = I('post.id');
        $content = '<option value="" class="default">--请选择区县--</option>';
        if (empty($cityId)) {
            echo $content;
            exit();
        }
        $Area = D('Area');
        $district = $Area->getDistrictByCityId($cityId);
        foreach($district as $val) {
            $content .='<option value="'.$val['id'].'">'.$val['name'].'</option>';
        }
        echo $content;
        exit();
    }

    public function set_default_address() {
        $json = array('success'=>1,'msg'=>'');
        if (!$this->accountObj->getAccountId()) {
            $json['success'] = 0;
            $json['msg'] = '请先登录！<script>window.location.href="'.U('Home/login/index').'"</script>';
            $this->ajaxReturn($json);
            exit();
        }
        $id = I('get.id');
        if (empty($id)) {
            $json['success'] = 0;
            $json['msg'] = '非法操作';
        } else {
            $result = $this->AccountAddress->setDefault($this->accountObj->getAccountId(),$id);
            if($result !== false) {
                $json['success'] = 1;
                $json['msg'] = '设置成功';
            } else {
                $json['success'] = 0;
                $json['msg'] = $this->AccountAddress->getDbError();
            }

        }
        $this->ajaxReturn($json);
    }

}