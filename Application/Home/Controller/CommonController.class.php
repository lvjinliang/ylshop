<?php
namespace Home\Controller;
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
        $searchKey = I('get.search');
        $this->assign('searchKey', $searchKey);

        // 推广种子记录
        $sourceCodeName = 'sourcecode';
        $sourceCodeValue = isset($_GET['_sc']) ? trim($_GET['_sc']) : '';
        // 记录来源CODE种子
        if($sourceCodeValue){
            $sourceCodeValue = strip_tags($sourceCodeValue);
            $time = 3600*24*30;  //默认一个月
            cookie($sourceCodeName,$sourceCodeValue,$time);
        }

        // 用户唯一标识种子
        $usersignName = 'usersign';
        $usersign = cookie($usersignName);
        $getUsersignCookieValue = !empty($usersign) ? $usersign : '';
        if(!$getUsersignCookieValue){
            $usersignValue = 'YL'.date('YmdHis').rand(10000,99999);
            $time = 3600*24*365*5;
            cookie($usersignName,$usersignValue,$time);
        }

        // 记录来源URL种子
        $sourceFromUrlName = 'sourceurl';
        $sourceFromUrlValue = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if(strpos($sourceFromUrlValue,'ylshop.com')){ // 本站不记录
            $sourceFromUrlValue = '';
        }
        if($sourceFromUrlValue){
            // 由静默期30天改为新来源直接覆盖
            $time = 3600*24*365;
            cookie($sourceFromUrlName,$sourceFromUrlValue,$time);

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