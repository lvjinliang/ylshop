<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends CommonController {
    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $this->_empty();
    }

}