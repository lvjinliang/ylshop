<?php
namespace Account\Controller;
use Think\Controller;
class OrderController extends CommonController {
    private $Order;
    private $error = array();
    public function _initialize() {
        parent::_initialize();
        $this->Order = D('Order');
    }

    public function index() {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '我的订单', 'href' => 'javascript:void(0)');
        $data = array();
        $filter = array();
        $filter['account_id'] = $this->accountObj->getAccountId();
        //分页
        $count = $this->Order->getTotal($filter);
        $page = setPage($count, C('PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $data['show']  = $page->homeShow();// 分页显示输出
        $filter['order'] = 'o.date_added DESC, o.id DESC ,o.id DESC';
        $data['orders'] = $this->Order->getList($filter);

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }

    public function detail() {
        if (!$this->accountObj->isLogin()) {
            urlRedirect(U('Home/login/index'));
            exit();
        }
        $breadcrumbs = array();
        $breadcrumbs[] = array('title' => '首页', 'href' => '/');
        $breadcrumbs[] = array('title' => '个人中心', 'href' => U('index/index'));
        $breadcrumbs[] = array('title' => '我的订单', 'href' =>  U('account/order/index'));
        $breadcrumbs[] = array('title' => '订单详情', 'href' =>  'javascript:void(0)');
        $order_no = I('get.id');
        if (empty($order_no)) {
            $this->_empty('请选择订单号');
        } else {
            $data['detail'] = $this->Order->getDetailByOrderNo($order_no);
        }

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('data', $data);
        $this->display();
    }

    public function ajax_return_order() {
        if (!$this->accountObj->isLogin()) {
            $json = array('success'=>0, 'msg'=>'请先登录');
            $this->ajaxReturn($json);
            exit();
        }
        $OrderObj = new \Org\Common\Order();
        $order_no = I('get.id');
        if(empty($order_no)) {
            $json = array('success'=>0, 'msg'=>'请选择操作订单');
            $this->ajaxReturn($json);
            exit();
        }
        $orderData = $this->Order->getDetailByOrderNo($order_no);
        if (empty($orderData)) {
            $json = array('success'=>0, 'msg'=>'订单不存在');
            $this->ajaxReturn($json);
            exit();
        }
        if ($orderData['order_status_id']==4) {
            $result = $OrderObj->updateOrderStatus($orderData['id'], 8);
            if($result!==false) {
                $data['order_id'] = $orderData['id'];
                $data['action_user'] = 'buyer';
                $data['order_status_id'] = 8;
                $data['payment_status_id'] = $orderData['payment_status_id'];
                $OrderObj->addOrderAction($data);
                $json = array('success'=>1, 'msg'=>'');
                $this->ajaxReturn($json);
                exit();
            } else {
                $json = array('success'=>0, 'msg'=>'操作失败');
                $this->ajaxReturn($json);
                exit();
            }
        } else {
            $json = array('success'=>0, 'msg'=>'还收到货不允许退货');
            $this->ajaxReturn($json);
            exit();
        }
    }

    public function ajax_cancel_order() {
        if (!$this->accountObj->isLogin()) {
            $json = array('success'=>0, 'msg'=>'请先登录');
            $this->ajaxReturn($json);
            exit();
        }
        $OrderObj = new \Org\Common\Order();
        $order_no = I('get.id');
        if(empty($order_no)) {
            $json = array('success'=>0, 'msg'=>'请选择操作订单');
            $this->ajaxReturn($json);
            exit();
        }
        $orderData = $this->Order->getDetailByOrderNo($order_no);
        if (empty($orderData)) {
            $json = array('success'=>0, 'msg'=>'订单不存在');
            $this->ajaxReturn($json);
            exit();
        }
        if (in_array($orderData['order_status_id'],array(1,2))) {
            $result = $OrderObj->cancel($orderData['id']);
            if($result!==false) {
                $data['order_id'] = $orderData['id'];
                $data['action_user'] = 'buyer';
                $data['order_status_id'] = 7;
                $data['payment_status_id'] = $orderData['payment_status_id'];
                $OrderObj->addOrderAction($data);
                $json = array('success'=>1, 'msg'=>'');
                $this->ajaxReturn($json);
                exit();
            } else {
                $json = array('success'=>0, 'msg'=>'已发货还允许取消');
                $this->ajaxReturn($json);
                exit();
            }

        }


    }

    public function ajax_confirm_order() {
        if (!$this->accountObj->isLogin()) {
            $json = array('success'=>0, 'msg'=>'请先登录');
            $this->ajaxReturn($json);
            exit();
        }
        $OrderObj = new \Org\Common\Order();
        $order_no = I('get.id');
        if(empty($order_no)) {
            $json = array('success'=>0, 'msg'=>'请选择操作订单');
            $this->ajaxReturn($json);
            exit();
        }
        $orderData = $this->Order->getDetailByOrderNo($order_no);
        if (empty($orderData)) {
            $json = array('success'=>0, 'msg'=>'订单不存在');
            $this->ajaxReturn($json);
            exit();
        }
        if ($orderData['order_status_id']==3) {
            $result = $OrderObj->updateOrderStatus($orderData['id'], 4);
            if($result!==false) {
                $data['order_id'] = $orderData['id'];
                $data['action_user'] = 'buyer';
                $data['order_status_id'] = 4;
                $data['payment_status_id'] = $orderData['payment_status_id'];
                $OrderObj->addOrderAction($data);
                $json = array('success'=>1, 'msg'=>'');
                $this->ajaxReturn($json);
                exit();
            } else {
                $json = array('success'=>0, 'msg'=>'操作失败');
                $this->ajaxReturn($json);
                exit();
            }
        } else {
            $json = array('success'=>0, 'msg'=>'订单还末发货，或系统异常，请联系卖家');
            $this->ajaxReturn($json);
            exit();
        }
    }




}