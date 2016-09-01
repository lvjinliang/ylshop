<?php
namespace Admin\Controller;

use Think\Controller;

class OrderController extends CommonController {

    protected $error = array();
    public $Order = '';

    public function _initialize() {
        parent::_initialize();
        $this->Order = D('Order');
    }

    public function index() {
        //查询条件
        $this->setTitle('订单列表');
        $filter = array();
        $search = array();

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'订单列表','href'=>U('order/index'));
        $data = array();
        $data['order_status'] = $this->Order->getAllOrderStatus();
        $data['payment_status'] = $this->Order->getAllPaymentStatus();

        if(I('get.order_no')) {
            $filter['o.order_no'] = array('like', I('get.order_no').'%');
            $search['order_no'] = I('get.order_no');
        } else {
            $search['order_no'] = '';
        }

        if(I('get.account_id')) {
            $filter['o.account_id'] = array('eq', I('get.account_id'));
            $search['account_id'] = I('get.account_id');
        } else {
            $search['account_id'] = '';
        }

        if(I('get.order_status_id')) {
            $filter['o.order_status_id'] = array('eq', I('get.order_status_id'));
            $search['order_status_id'] = I('get.order_status_id');
        } else {
            $search['order_status_id'] = '';
        }

        if(I('get.payment_status_id')) {
            $filter['o.payment_status_id'] = array('eq', I('get.payment_status_id'));
            $search['payment_status_id'] = I('get.payment_status_id');
        } else {
            $search['payment_status_id'] = '';
        }


        if(I('get.date_added')) {
            $filter['o.date_added'] = array(
                      array('lt', strtotime('+1 day',strtotime(I('get.date_added')))),
                      array('egt', strtotime(I('get.date_added')))
                    );
            $search['date_added'] = I('get.date_added');
        } else {
            $search['date_added'] = '';
        }


        //排序
        $filter['order'] = array('o.id'=>'DESC', 'o.date_added'=>'DESC');
        //分页
        $count = $this->Order->getTotal($filter);
        $page = setPage($count, C('ADMIN_PAGE_SIZE'));
        $filter['start'] = $page->firstRow;
        $filter['limit'] = $page->listRows;
        $show       = $page->adminShow();// 分页显示输出
        $lists = $this->Order->getLists($filter);
        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);

        $data['isShowViewButton'] = $this->AUTH->check(trim(U('order/view'),'/'), session('user.id'));

        $this->assign('success', $success);
        $this->assign('error', $error);

        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('search', $search);

        $this->assign('show', $show);
        $this->assign('lists', $lists);
        $this->assign('data', $data);
        $this->display();
    }

    public function view() {
        $this->setTitle('查看订单');
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'订单列表','href'=>U('order/index'));
        $id = I('get.id');
        if (!empty($id)) {
            $data = $this->Order->getDataById($id);
        }

        $this->assign('data', $data);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('Order/view');
    }

    public function cancel() {
        $OrderObj = new \Org\Common\Order();
        $json = array('success'=>0, 'msg'=>'');
        $id = I('get.id');
        if(empty($id)) {
            $json = array('success'=>0, 'msg'=>'请选择操作订单');
            $this->ajaxReturn($json);
            exit();
        }
        $orderData = $this->Order->getDataById($id);
        if (empty($orderData)) {
            $json = array('success'=>0, 'msg'=>'订单不存在');
            $this->ajaxReturn($json);
            exit();
        }
        $resutl = $OrderObj->cancel($orderData['id'], 2);
        if ($resutl !== false ) {
            $data['order_id'] = $orderData['id'];
            $data['action_user'] = session('user.name');
            $data['order_status_id'] = 7;
            $data['payment_status_id'] = $orderData['payment_status_id'];
            $OrderObj->addOrderAction($data);
            $json = array('success'=>1, 'msg'=>'');
            $this->ajaxReturn($json);
        } else {
            $json = array('success'=>0, 'msg'=>'操作失败');
            $this->ajaxReturn($json);
            exit();
        }
    }

}