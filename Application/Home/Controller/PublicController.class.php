<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends CommonController {
    public function _initialize() {
        parent::_initialize();
    }

    public function verify() {
        $Verify = new \Think\Verify();
        $Verify->fontSize = 18;
        $Verify->useCurve = false;
        $Verify->length = 4;
        $Verify->imageW = 120;
        $Verify->imageH = 40;
        $Verify->entry();
    }

    public function test() {
        $Promotions = D('Promotions');
        $aaa = $Promotions->getPromotionsGoods();
        print_r($aaa);
    }


}