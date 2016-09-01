<?php
namespace Home\Controller;
use Think\Controller;
class BiController extends CommonController {
    public function _initialize() {
        parent::_initialize();
    }

    public function index () {
        layout(false);
        $param = I('post.param');
        $param = unserialize(base64_decode(urldecode($param)));
        $param['type'] = 'WEB';
        $param['sub_type'] = '';
        $param['pt'] = I('post.pt');
        $Bi = new \Org\Common\Bi();
        $Bi->add($param);
    }



}