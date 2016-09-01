<?php
namespace Admin\Controller;

use Think\Controller;

class LogoutController extends Controller {
    private $error = array();

    public function _initialize() {
        header('Content-Type: text/html; charset=utf-8');
    }

    public function index () {
        session('user',null);
        $redirect = U('Admin/login/index');
        redirect($redirect);
    }




}