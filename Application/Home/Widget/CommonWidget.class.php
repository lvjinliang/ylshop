<?php
/**
 * User: 良子
 * Date: 16-2-29
 */
namespace Home\Widget;
use Think\Controller;
class CommonWidget extends Controller {
    private $Ad;
    private $accountObj;
    public function _initialize() {
        $this->accountObj = \Org\Home\Account::getInstance();
        $this->Ad = D('Ad');
    }
    public function menu() {
        $Category = D('Category');
        $data = $Category->getCategory();
        $this->assign('data', $data);
        $this->display(T('Home@Widget:menu'));
    }


    public function main_ad($code) {
        $images = $this->Ad->getDataByCode($code);
        $this->assign('images', $images);
        $this->display(T('Home@Widget:main_ad'));
    }


    public function col_banner($code,$col=3) {
        $images = $this->Ad->getDataByCode($code);
        $this->assign('images', $images);
        $this->assign('col', $col);
        $this->display(T('Home@Widget:col_banner'));
    }

    public function init_cart() {
        echo \Org\Home\Cart::getCartNumber();
    }

    public function bi() {
        $catid = '';
        if (stripos(strtolower(__ACTION__),'category/index') !== false ) {
            $catid = I('get.id');
        }
        $id = '';
        if (stripos(strtolower(__ACTION__),'goods/index') !== false ) {
            $id = I('get.id');
        }
        $server = array();
        $server['cururl'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $server['sourceurl'] = cookie('sourceurl') ? cookie('sourceurl') : '';
        $server['sc'] = cookie('sourcecode') ? cookie('sourcecode') : '';
        $server['us'] = cookie('usersign') ? cookie('usersign') : '';
        $server['ua'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $server['ip'] = get_client_ip();
        $server['sid'] = session_id();
        $server['catid'] = $catid;
        $server['id'] = $id;
        $server['account_id'] = $this->accountObj->getAccountId();
        $server['pagename'] = strtolower(__ACTION__);
        $param = urlencode(base64_encode(serialize($server)));
        $this->assign('param', $param);
        $this->assign('url', U('Home/bi/index'));
        $this->display(T('Home@Widget:bi'));
    }



}
?>