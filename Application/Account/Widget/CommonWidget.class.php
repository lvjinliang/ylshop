<?php
/**
 * User: 良子
 * Date: 16-2-29
 */
namespace Account\Widget;
use Think\Controller;
class CommonWidget extends Controller {

    public function menu() {
        $data = array();

        $data[] = array('href'=>U('index/index'), 'title'=>'个人主页');
        $data[] = array('href'=>U('profile/index'), 'title'=>'用户信息');
        $data[] = array('href'=>U('order/index'), 'title'=>'我的订单');
        $data[] = array('href'=>U('review/index'), 'title'=>'我的评价');
        //$data[] = array('href'=>U('collection/index'), 'title'=>'我的收藏');
        $data[] = array('href'=>U('integral/index'), 'title'=>'我的积分');
        $data[] = array('href'=>U('transaction/index'), 'title'=>'我的资金');
        $data[] = array('href'=>U('collection/index'), 'title'=>'优惠券');
        $this->assign('data', $data);
        $this->assign('currentUrl', MODULE_NAME.'/'.CONTROLLER_NAME);
        $this->display(T('Account@Widget:menu'));
    }






}
?>