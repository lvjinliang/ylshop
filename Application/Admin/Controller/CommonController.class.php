<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
    protected $AUTH = '';
    public function _initialize() {
        header('Content-Type: text/html; charset=utf-8');
        if( !session('?user.id')) {
            $this->redirect('login/index');
        }
        $this->setTitle();
        $this->setKeywords();
        $this->setDescription();
        $this->AUTH = new \Think\Auth();
        $DB_Config = D('Config');
        $commonConfig = $DB_Config->getAll();
        foreach($commonConfig as $key => $val ){
             C(strtoupper($key), $val);
        }
        if(!$this->AUTH->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME, session('user.id'))){
            $allAuthLink = $this->AUTH->getAllAuth();
            if (in_array(trim(strtolower(__ACTION__),'/'),$allAuthLink)) {
                $this->no_auth();
                exit();
            }

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


    public function no_auth () {
        $this->display('Common/no_auth');
    }



}