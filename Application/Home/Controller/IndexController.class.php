<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $IpLocation = new \Org\Net\IpLocation();
        $this->display();
    }
}