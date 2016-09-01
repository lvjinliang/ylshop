<?php
namespace Admin\Controller;

use Think\Controller;

class ReportOrderController extends CommonController {

    private $Order = '';

    public function _initialize() {
        parent::_initialize();
        $this->Order= D('Order');
    }

    public function index() {
        //查询条件
        $this->setTitle('订单统计');
        $filter = array();
        $search = array();
        $breadcrumbs = array();
        $breadcrumbs[] = array('title'=>'首页','href'=>U('index/index'));
        $breadcrumbs[] = array('title'=>'订单统计','href'=>U('report_order/index'));


        if(I('get.start_date')) {
            $startDate = I('get.start_date');
            $search['start_date'] = I('get.start_date');
        } else {
            $startDate = date('Y-m-d', strtotime('-7 day'));
            $search['start_date'] = $startDate;
        }
        if(I('get.end_date')) {
            $endDate = I('get.end_date');
            $search['end_date'] = I('get.end_date');
        } else {
            $endDate = date('Y-m-d', strtotime('-1 day'));
            $search['end_date'] = $endDate;
        }

        $filter['start_date'] = $startDate;
        $filter['end_date'] = $endDate;

        //排序
        $filter['order'] = array( 'date'=>'asc');
        $type = I('get.type') ;
        if(empty($type)) {
            //分页
            $count = $this->Order->getOrderDateTotal($filter);
            $page = setPage($count, C('ADMIN_PAGE_SIZE'));
            $filter['start'] = $page->firstRow;
            $filter['limit'] = $page->listRows;
            $show  = $page->adminShow();// 分页显示输出
            $lists = $this->Order->getOrderDateList($filter);
        } elseif ($type=='download') {
            $lists = $this->Order->getOrderDateList($filter,$type='report');
            $xlsName  = "订单统计";
            $xlsCell  = array(
                array('setting_date','日期'),
                array('all_rows','总订单数'),
                array('all_total','总订单额'),
                array('pay_rows','支付单数'),
                array('pay_total','支付额'),
                array('back_rows','取消单数'),
            );
            vendor("Excel.excel");
            $Excel = new \Excel();
            $Excel->write($xlsName,$xlsCell, $lists);
            exit();
        }
        $downLoadParam = $search;
        $downLoadParam['type'] = 'download';

        $success = session('?success')?session('success'):false;
        session('success',null);
        $error = session('?error')?session('error'):false;
        session('error',null);
        $this->assign('success', $success);
        $this->assign('error', $error);
        $this->assign('breadcrumbs', $breadcrumbs);
        $this->assign('show', $show);
        $this->assign('search', $search);
        $this->assign('lists', $lists);
        $this->assign('downloadUrl', U('report_order/index',$downLoadParam));
        $this->display();
    }


}