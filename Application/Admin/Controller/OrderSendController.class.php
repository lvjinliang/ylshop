<?php
namespace Admin\Controller;

use Think\Controller;

class OrderSendController extends CommonController {

    protected $error = array();
    protected $payment_status_id = '';
    public $Order = '';

    public function _initialize() {
        parent::_initialize();
        $this->Order = D('Order');
    }

    public function index() {
        //查询条件
        $this->setTitle('送货订单');

        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'送货订单','href'=>U('order_sent/index'));
        $content = $this->content();
        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('content', $content);
        $this->assign('contentUrl', U('order_send/ajax_get_content'));
        $this->display();
    }

    private function content () {
        $data = array();
        $filter = array();
        $search = array();

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

        if(I('get.payment_status_id')) {
            $filter['o.payment_status_id'] = array('eq', I('get.payment_status_id'));
            $search['payment_status_id'] = I('get.payment_status_id');
        } else {
            $search['payment_status_id'] = '';
        }

        $filter['o.order_status_id'] = 3;

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

        $data['isSHowConfirmButton'] = $this->AUTH->check(trim(U('order_send/confirm'),'/'), session('user.id'));
        $this->assign('search', $search);
        $this->assign('show', $show);
        $this->assign('lists', $lists);
        $this->assign('data', $data);
        return $this->fetch('Order_send/content');
    }

    public function edit() {
        $this->setTitle('查看订单');
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'确认收获','href'=>U('order_send/index'));
        $id = I('get.id');
        $redirect = I('post.redirect');
        if(!$redirect) {
            $redirect = isset($_SERVER['HTTP_REFERER'])&&!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']: U('order_send/index');
        }

        if (empty($id)) {
            session('error','非法操作');
            redirect($redirect);
        } else {
            $data = $this->Order->getDataById($id);
        }

        $this->payment_status_id = $data['payment_status_id'];
        if ( IS_POST && $this->validate_form()) {
            $OrderObj = new \Org\Common\Order();
            $result = $OrderObj->updateOrder(I('post.'), $data);
            if($result!==false) {
                session('success','修改成功');
            } else {
                session('error','修改失败');
            }
            redirect($redirect);
        } else {
            $errorInfo = '';
            if (!empty($this->error)) {
                $errorInfo = '含有不正确信息，请检查红色部分';
            }
            if (IS_POST) {
                $data['order_status_id'] = I('post.order_status_id');
                $data['invoice_status'] = I('post.invoice_status');
                $data['invoice_type'] = I('post.invoice_type');

                $data['action_note'] = I('post.action_note');

                $data['shipping_name'] = I('post.shipping_name');
                $data['shipping_telephone'] = I('post.shipping_telephone');
                $data['shipping_province_id'] = I('post.province');
                $data['shipping_city_id'] = I('post.city');
                $data['shipping_district_id'] = I('post.district');
                $data['shipping_address'] = I('post.shipping_address');
                $data['shipping_postcode'] = I('post.shipping_postcode');

                $data['update_total'] = I('post.update_total');
                $data['update_total_note'] = I('post.update_total_note');
            }
        }

        $Area = new \Org\Common\Area();
        $data['provinces'] = $Area->getProvinces();
        $data['citys'] = array();
        if (!empty($data['shipping_province_id'])) {
            $data['citys'] = $Area->getCityByProvinceId($data['shipping_province_id']);
        }
        $data['districts'] = array();
        if (!empty($data['shipping_city_id'])) {
            $data['districts'] = $Area->getDistrictByCityId($data['shipping_city_id']);
        }

        $this->assign('redirect', $redirect);
        $this->assign('data', $data);
        $this->assign('error', $this->error);
        $this->assign('errorInfo', $errorInfo );
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->display('Order_send/edit');
    }


    public function ajax_get_content () {
        echo $this->content();
        exit();
    }


    public function confirm () {
        $OrderObj = new \Org\Common\Order();

        $id = I('get.id');
        if(empty($id)) {
            $json = array('success'=>0, 'msg'=>'请选择操作订单');
            $this->ajaxReturn($json);
            exit();
        }
        $orderData = $OrderObj->getDataById($id);
        if (empty($orderData)) {
            $json = array('success'=>0, 'msg'=>'订单不存在');
            $this->ajaxReturn($json);
            exit();
        }

        $resutl = $OrderObj->updateOrderStatus($orderData['id'],4);
        if ($resutl !== false ) {
            $data['order_id'] = $orderData['id'];
            $data['action_user'] = session('user.name');
            $data['order_status_id'] = 4;
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