<?php
namespace Account\Controller;
use Think\Controller;
class CommonController extends Controller {
    protected $accountObj = null;
    public function _initialize() {
        header('Content-Type: text/html; charset=utf-8');
        $this->setTitle();
        $this->setKeywords();
        $this->setDescription();
        $this->setScript();
        $this->setStyle();
        $DB_Config = D('Admin/Config');
        $commonConfig = $DB_Config->getAll();
        foreach($commonConfig as $key => $val ) {
            C(strtoupper($key), $val);
        }
        $this->assign('showMenu', 1);
        $this->accountObj = \Org\Home\Account::getInstance();
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
    }


    protected function  setTitle( $title='' ) {
        $title = $title?$title:'雨良商务平台';
        $this->assign('seoTitle', $title);
    }

    protected function  setKeywords( $keywords='' ) {
        $keywords = $keywords?$keywords:'雨良商务平台';
        $this->assign('seoKeywords', $keywords);
    }

    protected function  setDescription( $description='' ) {
        $description = $description?$description:'雨良商务平台';
        $this->assign('seoDescription', $description);
    }

    protected function setScript($scripts=array()){
        $this->assign('scripts', $scripts);
    }
    protected function setStyle($styles=array()){
        $this->assign('styles', $styles);
    }

    public function _empty($notice='没找到相应的页面') {
        header('HTTP/1.1 404 Not Found');
        $this->assign('notice',$notice);
        $this->display(T('Home@Empty/notfind'));
    }

}